<?php

namespace App\Importers;

use \DB;
use App\Value;
use App\Profile;
use Carbon\Carbon;
use App\Events\NewValues;
use App\Events\CheckProfiles;
use App\Parsers\DataSets\DataSet;
use App\Events\BeforeValuesInserted;

class Importer
{
    /**
     * Stores the data set to import
     *
     * @var App\Parsers\DataSets\DataSet
     */
    protected $dataSet;

    /**
     * The code name of the currentProfile
     *
     * @var string
     */
    protected $currentProfile=null;

    /**
     * Load the data set and store it
     *
     * @param  DataSet $dataSet the data set to import
     * @return Importer         $this
     */
    public function load(DataSet $dataSet)
    {
        $this->dataSet = $dataSet;

        return $this;
    }

    /**
     * Compute the process to insert all values prensent
     * in the data set and check all constraints attached
     *
     * @return Importer         $this
     */
    public function import()
    {
        $this->insertValues()
             ->checkProfiles();

        return $this;
    }

    /**
     * Browse the complete data set loaded and create
     * a new Value based on the retreived informations
     * The new value has passed to a new "NewValue" event
     *
     * @return Importer         $this
     */
    protected function insertValues()
    {
      $this->dataSet->resetCursors();
      $values = array();
      while ($this->dataSet->hasNextValue())
      {
        list($profile, $data, $value, $time) = $this->dataSet->getNextValue();

        if($this->currentProfile!=$profile)
        {
          //Verifie que ce n'est pas le premier profile
          if($this->currentProfile!=null)
          {
            $this->push($values);
          }
          //définition du profile courant
          $this->currentProfile=$profile;
          //reset le profile.
          $values = array();

          $limitTime = $this->getDatabaseTime();
          $minutes = 11 * 10;
          $limitTime = $limitTime->copy()->subMinutes($minutes);
        }

        if($time > $limitTime)
        {
          $values[] = new Value([
              'profile_stn_code' => $profile,
              'data_code' => $data,
              'date' => $time,
              'value' => $value,
              'tag' => null,
          ]);
        }
      }

      //Add the last profile
      $this->push($values);

      return $this;
    }

    /**
     * Call the "ValueInserterd" event to fire the
     * post checks
     * @param  string  $currentProfile the profile
     * @return Importer          $this
     */
    protected function beforeValuesInserted($currentProfile)
    {
        event(new BeforeValuesInserted ($currentProfile));

        return $this;
    }

    /**
     * Push the values
     * @param  array  $data
     * @return void
     */
    protected function push(array $data)
    {
      if(count($data)>0){
        DB::beginTransaction();
          $this->beforeValuesInserted($this->currentProfile);
          event(new NewValues($data));
        DB::commit();
      }
    }

    /**
     * Browse all profiles present in the data set and
     * retreive it form the database to fire the "CheckProfiles"
     * event. CheckProfile verify if the online field on the profile
     * must be updated
     *
     * @return Importer           $this
     */
    protected function checkProfiles()
    {
        $this->dataSet->resetCursors();
        while ($this->dataSet->hasNextProfile())
        {
            $p = $this->dataSet->getNextProfile();
            $profile = Profile::find($p);
            event(new CheckProfiles($profile));
        }

        return $this;
    }

    /**
     * Retreive the latest update datetime from profiles
     * and set the databaseUpdateTime. If no profile was
     * found, then the databaseUpdateTime is set to null
     *
     * @return Carbon\Carbon or null if no profile in database
     */
    private function getDatabaseTime()
    {
        $res = DB::table('profiles')
                      ->where('stn_code', '=', $this->currentProfile)
                      ->max('last_update');
        if ($res == null) return null;
        else return new Carbon($res);
    }
}
