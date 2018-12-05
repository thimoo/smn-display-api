<?php

namespace App\Importers;

use App\Value;
use App\Profile;
use App\Events\NewValue;
use App\Events\CheckProfiles;
use App\Events\BeforeValuesInserted;
use App\Parsers\DataSets\DataSet;

class Importer
{
    /**
     * Stores the data set to import
     *
     * @var App\Parsers\DataSets\DataSet
     */
    protected $dataSet;

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
        $this->BeforeValuesInserted()
             ->insertValues()
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
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("<info>Start : ".date("H:i:s")."</info>");
        $currentProfile=null;
        $this->dataSet->resetCursors();
        while ($this->dataSet->hasNextValue())
        {
            list($profile, $data, $value, $time) = $this->dataSet->getNextValue();
            if($currentProfile!=$profile)
            {
              $output->writeln("<info>".$profile." ".date("H:i:s")."</info>");
              $currentProfile=$profile;
            }

            $value = new Value([
                'profile_stn_code' => $profile,
                'data_code' => $data,
                'date' => $time,
                'value' => $value,
                'tag' => null,
            ]);
            event(new NewValue($value));
        }
        $output->writeln("<info>Start : ".date("H:i:s")."</info>");

        return $this;
    }

    /**
     * Call the "ValueInserterd" event to fire the
     * post checks
     *
     * @return Importer          $this
     */
    protected function BeforeValuesInserted()
    {
        event(new BeforeValuesInserted);

        return $this;
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
      $output = new \Symfony\Component\Console\Output\ConsoleOutput();
      $output->writeln("<info>checkProfiles</info>");

        $this->dataSet->resetCursors();
        while ($this->dataSet->hasNextProfile())
        {
            $p = $this->dataSet->getNextProfile();
            $profile = Profile::find($p);
            event(new CheckProfiles($profile));
        }

        return $this;
    }
}
