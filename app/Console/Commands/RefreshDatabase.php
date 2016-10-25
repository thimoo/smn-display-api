<?php

namespace App\Console\Commands;

use \DB;
use Carbon\Carbon;
use App\Parsers\CsvParser;
use App\Importers\Importer;
use Illuminate\Console\Command;
use \GuzzleHttp\Client as HttpClient;

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
     *
     * @return mixed
     */
    public function handle()
    {
        // Starting process
        $this->info("Starting refresh...");

        // Loading target url
        $csvTargetUrl = config('constants.csv_target_url');
        $this->info("Getting csv at: $csvTargetUrl");

        // Create a new Http client and make the request
        $client = new HttpClient();
        $response = $client->request('GET', $csvTargetUrl);

        // if the request is successfull
        $status = $response->getStatusCode();
        if ($status < 300) {
            $this->info("Response ok [$status]");
            $this->csvStatusOk($response);
        } else {
            $this->csvStatusError();
        }
    }

    private function csvStatusOk($response)
    {
        $content = $response->getBody()->getContents(); // string

        // create the parser and pass the content
        $parser = (new CsvParser($content))->parse();

        if ($parser->validateFormat()) {
            // Get the CSV object
            $dataSet = $parser->get();

            // $dataSet->display();

            if ($this->databaseMustBeUpdated($dataSet)) {
                $this->info('Database must be updated!');
                $importer = new Importer;
                $importer->load($dataSet)->import();
            }
        }
    }

    private function csvStatusError()
    {
        $this->error('Something went wrong to retreive the CSV!');

        // log the error

        if ($this->databaseMustBeUpdated()) {
            // TODO
            // insert all no-data values
            $this->info('Inserting no-data values...');
        }
    }

    private function databaseMustBeUpdated($dataSet = null)
    {
        return true;

        $datetime = Carbon::now();

        if ($dataSet)
        {
           $datetime = $dataSet->datetime();
        }

        $lastUpdate = $this->lastUpdate();
        if ($lastUpdate == null)
            return true;

        $lastUpdate->addMinutes(10);

        return $datetime->gte($lastUpdate);
    }

    private function lastUpdate()
    {
        return new Carbon(DB::table('profiles')->max('last_update'));
    }
}
