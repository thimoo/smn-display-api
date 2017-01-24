<?php

namespace App\Console\Commands;

use \DB;
use \Log;
use App\Data;
use App\Profile;
use Carbon\Carbon;
use App\Events\NoValues;
use App\Parsers\CsvParser;
use App\Importers\Importer;
use Illuminate\Console\Command;
use App\Parsers\DataSets\DataSet;
use GuzzleHttp\Client as HttpClient;
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
     * 
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
     * Store the importer used to import the DataSet
     * 
     * @var App\Importers\Importer
     */
    private $importer;

    /**
     * Store the parser used to transform the CSV in DataSet
     * 
     * @var App\Parsers\CsvParser
     */
    private $parser;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->importer = new Importer;
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
        DB::beginTransaction();

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
            if ($status >= 200 && $status < 300)
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

        DB::commit();
        $this->info("Refresh finished!");
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
        $this->parser = (new CsvParser($content))->parse();

        if ($this->parser->validateFormat())
        {
            $this->insertNewValues();
        }
        else 
        {
            $this->insertNoDataValues();
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
            $this->insertNoDataValues();
        }
        else
        {
            $this->noUpdateNeeded();
        }
    }

    private function insertNewValues()
    {
        // The dataset represent with a usefull way
        // the collections of data present in the
        // CSV. The dataset is used to pass data
        // between the parser and the importer
        $dataSet = $this->parser->get();

        // Check if the database must be updated
        // based on the dataset datetime, if yes:
        // import all data
        if ($this->databaseMustBeUpdated($dataSet)) 
        {
            $this->info('Database must be updated!');
            $this->displayDates();
            $this->importer->load($dataSet)->import();
        }
        else
        {
            $this->noUpdateNeeded();
        }
    }

    private function insertNoDataValues()
    {
        $this->info('Inserting no-data values...');
        $this->displayDates();

        // No data must be imported in the database with
        // the current time rounded
        $forDate = $this->computeNowDate();
        $this->info("No-data date: $forDate");

        // Retreive all profiles
        $forProfiles = Profile::all();

        // Retreive all data
        $forData = Data::all();

        // Generate a new DataSet with no-data
        // values for the date, profiles and data
        $dataSet = (new DataSet)->populate(
            $forDate, 
            $forProfiles, 
            $forData
        );
        
        // Import the DataSet
        $this->importer->load($dataSet)->import();
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
            // When the application start and if
            // no CSV is retreived, then we must 
            // compute a datetime based on the
            // current time
            return $this->computeNowDate();
        }

        // Add ten minutes to the last datetime present
        // in database
        return (new Carbon($this->databaseUpdateTime))->addMinutes(10);
    }

    /**
     * Get the current datetime and create a Carbon
     * date with the minutes rouded to the nearest
     * ten below minus ten
     * 
     * @return Carbon\Carbon the now datetime rounded
     */
    private function computeNowDate()
    {
        $year = date('Y');
        $month = date('n');
        $day = date('j');

        $hour = date('G');

        // Rounding the number of seconds to ten below
        // ex: 20 -> 20, 34 -> 30, 58 -> 50
        $minute = (int) (date('i') / 10);
        $minute *= 10;

        $tz = date('e');
        $new = Carbon::create($year, $month, $day, $hour, $minute, 0, $tz);
        $new->subMinutes(10);

        // If a datetime is present in database,
        // then check if minus ten is greather
        // than, else add ten
        if ($this->databaseUpdateTime && $this->databaseUpdateTime->gte($new))
        {
            $new->addMinutes(10);
        }

        return $new;
    }

    /**
     * Log the error
     * 
     * @return void
     */
    private function logTheError()
    {
        $this->error('Something went wrong to retreive the CSV!');
        Log::error("The CSV can't be retreive");
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
        $nextUpdateTime = $this->nextUpdateTime ?? 'no date';
        $databaseUpdateTime = $this->databaseUpdateTime ?? 'no date';

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
        if ($this->databaseUpdateTime === null) return true;

        // If a dataset is present, then retreive the 
        // datetime of the content. If the datetime of
        // the content is greater than the database, then
        // the database must be udpated
        if ($dataSet !== null)
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
     * Retreive the latest update datetime from profiles
     * and set the databaseUpdateTime. If no profile was
     * found, then the databaseUpdateTime is set to null
     * 
     * @return void
     */
    private function getLastUpdate()
    {
        $res = DB::table('profiles')->max('last_update');
        if ($res === null) $this->databaseUpdateTime = null;
        else $this->databaseUpdateTime = new Carbon($res);
    }
}
