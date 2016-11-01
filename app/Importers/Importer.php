<?php

namespace App\Importers;

use App\Value;
use App\Events\NewValue;
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
     * Browse the complete data set loaded and create
     * a new Value based on the retreived informations
     * The new value has passed to a new "NewValue" event
     * 
     * @return Importer  $this
     */
    public function import()
    {
        while ($this->dataSet->hasNextValue()) {
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

        event(new ValuesInserted);

        return $this;
    }
}
