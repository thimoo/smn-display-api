<?php

namespace App\Importers;

use App\Value;
use App\Profile;
use App\Events\NewValue;
use App\Events\CheckProfiles;
use App\Events\ValuesInserted;
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
        $this->insertValues()
             ->valuesInserted()
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
        while ($this->dataSet->hasNextValue()) 
        {
            list($profile, $data, $value, $time) = $this->dataSet->getNextValue();

            $value = new Value([
                'profile_stn_code' => $profile,
                'data_code' => $data,
                'date' => $time,
                'value' => $value,
                'tag' => null,
            ]);

            event(new NewValue($value));
        }

        return $this;
    }

    /**
     * Call the "ValueInserterd" event to fire the
     * post checks
     * 
     * @return Importer          $this
     */
    protected function valuesInserted()
    {
        event(new ValuesInserted);

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
