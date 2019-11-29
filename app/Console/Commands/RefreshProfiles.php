<?php

namespace App\Console\Commands;

use \DB;
use \StdClass;
use App\Profile;
use Illuminate\Console\Command;

class RefreshProfiles extends Command
{
    /**
     * Index in line for the stn_code
     */
    const STN_CODE = 0;

    /**
     * Index in line for the name station
     */
    const NAME = 1;

    /**
     * Index in line for the altitude station
     */
    const ALTITUDE = 2;

    /**
     * Index in line for the altitude station
     */
    const ALTITUDETOWZ = 3;

    /**
     * If true the db transaction is commit, else
     * the transaction is rollback
     *
     * @var boolean
     */
    protected $commit = true;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profiles:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload the data profiles contained in the csv';

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
        $this->parse();
    }

    /**
     * Parse the csv file at the path given in the constants
     * file
     *
     * @return void
     */
    private function parse()
    {
        // Load the file and
        // parse the csv format
        $path = storage_path(config('constants.stations_infos_path'));

        if (($pointer = fopen($path, 'r')))
        {
            DB::beginTransaction();
            $first = true;
            while (($line = fgetcsv($pointer, 0, ';')))
            {
                // Ignore the first header line
                if (! $first)
                {
                    $this->refresh($line);
                }
                $first = false;
            }
            fclose($pointer);

            if ($this->commit)
            {
                DB::commit();
            }
            else
            {
                DB::rollBack();
            }
        }
    }

    /**
     * Try to retreive the profile and check if informations
     * already exists, if not update the profile
     *
     * @param  array  $line the current line in the csv
     * @return void
     */
    private function refresh(array $line)
    {
        // Get the profile
        $code = Profile::normalizeCode($line[self::STN_CODE]);
        $profile = Profile::find($code);

        // Check if update needed
        if ($profile && ( (! isset($profile->infos->name) && ! isset($profile->infos->altitude) && ! isset($profile->infos->altitude) )
          || $profile->infos->name !== $line[self::NAME]
          || (isset ($line[self::ALTITUDETOWZ])
          && (!isset ($profile->infos->altitudeTowz) || $profile->infos->altitudeTowz !== $line[self::ALTITUDETOWZ]) ) ) )
        {
            // Update needed
            $this->info("Refresh the profile '$profile->stn_code'...");
            $this->update($profile, $line);
        }
    }

    /**
     * Update the given profile with the array of infos
     *
     * @param  Profile $profile the profile to update
     * @param  array   $infos   the new infos
     * @return void
     */
    private function update(Profile $profile, array $infos)
    {
        $newInfo = new StdClass();
        $newInfo->name = $infos[self::NAME];
        $newInfo->altitude = $infos[self::ALTITUDE];

        if($infos[self::ALTITUDETOWZ] !=='')
        {
          $newInfo->altitudeTowz = $infos[self::ALTITUDETOWZ];
        }

        $profile->setNewInfos($newInfo);

        if ($profile->save())
        {
            $this->info("'$profile->stn_code' has been refreshed !");
        }
        else
        {
            $this->commit = false;
        }
    }
}
