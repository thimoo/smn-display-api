<?php

namespace App\Console\Commands;

use \DB;
use \Log;
use \Mail;
use Carbon\Carbon;
use App\Events\NoValues;
use App\Parsers\CsvParser;
use App\Importers\Importer;
use Illuminate\Console\Command;
use \GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;

class RefreshDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshed the database by loading the CSV';

    /**
     * Store the datetime of the last update in DB
     * @var \Carbon\Carbon
     */
    private $databaseUpdateTime;

    /**
     * Store the datetime found for the current update
     * 
     * @var \Carbon\Carbon
     */
    private $nextUpdateTime;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Create an Http client and download the CSV
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Starting refresh...");

        // Loading the target url in the config file
        $csvTargetUrl = config('constants.csv_target_url');
        $this->info("Getting csv at: $csvTargetUrl");

        // Create a new Http client and make the request
        $client = new HttpClient();
        
        try
        {
            // If the GET is successful, then trigger
            // the proccess to parse it and import it
            // else, an error proccess how log and notify
            // the user is trigger
            $response = $client->request('GET', $csvTargetUrl);

            $status = $response->getStatusCode();
            if ($status < 300) 
            {
                $this->info("Response ok [$status]");
                $this->csvStatusOk($response);
            } 
            else 
            {
                $this->csvStatusError();
            }    
        } 
        catch (ClientException $e) 
        {
            $this->csvStatusError();
        }
    }

    /**
     * If the CSV is successfully downloaded, then a
     * parser is created. The parser take the content
     * as a string and output a dataset object.
     * The dataset is pass to an importer that fire all
     * necessary events.
     * 
     * @param  object
     * @return void
     */
    private function csvStatusOk($response)
    {
        // Retreive the content of the CSV file
        // the method return a string
        $content = $response->getBody()->getContents();

        // Create the parser, parse the content
        // and validate the format
        $parser = (new CsvParser($content))->parse();

        if ($parser->validateFormat()) 
        {
            // The dataset represent with a usefull way
            // the collections of data present in the
            // CSV. The dataset is used to pass data
            // between the parser and the importer
            $dataSet = $parser->get();

            // Check if the database must be updated
            // based on the dataset datetime, if yes:
            // create the importer and import all data
            if ($this->databaseMustBeUpdated($dataSet)) 
            {
                $this->info('Database must be updated!');
                $this->displayDates();
                $importer = new Importer;
                $importer->load($dataSet)->import();
            }
            else
            {
                $this->noUpdateNeeded();
            }
        }
    }

    /**
     * If an error occured, check if the database must
     * be updated, if yes, then fire an event for inserting
     * no-data values.
     * 
     * @return void
     */
    private function csvStatusError()
    {
        $this->logTheError();

        if ($this->databaseMustBeUpdated()) 
        {
            $this->info('Inserting no-data values...');
            $this->displayDates();

            // No data must be imported in the database with
            // the next time
            $forDate = $this->computeNextDatetime();
            $this->info("No-data date: $forDate");
            event(new NoValues($forDate));
        }
        else
        {
            $this->noUpdateNeeded();
        }
    }

    /**
     * Get the next datetime used to set the date field
     * on no-data values when CSV error occured
     * 
     * @return Carbon\Carbon
     */
    private function computeNextDatetime()
    {
        if ($this->databaseUpdateTime == null)
        {
            // carbon now
            // arrondir à la dizaine de minutes 
            return null;
        }

        // Add ten minutes to the last datetime present
        // in database
        return (new Carbon($this->databaseUpdateTime))->addMinutes(10);
    }

    /**
     * Log the error and make an email if possible
     * 
     * @return void
     */
    private function logTheError()
    {
        $this->error('Something went wrong to retreive the CSV!');
        Log::error("The CSV can't be retreive");
        // TODO send a mail
    }

    /**
     * Display that the update is not needed
     * and display dates
     * 
     * @return void
     */
    private function noUpdateNeeded()
    {
        $this->info('No update needed!');
        // Pretty display the two dates
        $this->displayDates();
    }

    /**
     * Display the date used to check if the database
     * must be updated or not
     * 
     * @return void
     */
    private function displayDates()
    {
        $nextUpdateTime = $this->nextUpdateTime;
        $databaseUpdateTime = $this->databaseUpdateTime;

        $this->info("Next update date found: $nextUpdateTime");
        $this->info("Last database udpate: $databaseUpdateTime");
    }

    /**
     * If a dataset is define, then the datetime is
     * set to the dataset datetime. Else the current
     * time is used. The check is performed on the 
     * lastest last_update datetime profile
     *
     * @param  App\Parsers\DataSets\DataSet  $dataset
     * @return bool
     */
    private function databaseMustBeUpdated($dataSet = null)
    {
        $this->getLastUpdate();
        $this->nextUpdateTime = Carbon::now();

        // If no last_update is found, then the database
        // are not yet be populate, database must be 
        // updated
        if ($this->databaseUpdateTime == null) return true;

        // If a dataset is present, then retreive the 
        // datetime of the content. If the datetime of
        // the content is greater than the database, then
        // the database must be udpated
        if ($dataSet != null)
        {
           $this->nextUpdateTime = $dataSet->datetime();
           return $this->nextUpdateTime->gt($this->databaseUpdateTime);
        }
        // If no dataset is present, then check if
        // the current date is greater than the last
        // update found in database
        else
        {
            $lastUpdate = new Carbon($this->databaseUpdateTime);
            $lastUpdate->addMinutes(10);
            return $this->nextUpdateTime->gte($lastUpdate);
        }
    }

    /**
     * Retreive the latest update datetime of profiles
     * 
     * @return void
     */
    private function getLastUpdate()
    {
        $res = DB::table('profiles')->max('last_update');
        if ($res == null) $this->databaseUpdateTime = null;
        else $this->databaseUpdateTime = new Carbon($res);
    }
}
