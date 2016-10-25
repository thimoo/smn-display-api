<?php

namespace App\Importers;

use App\Value;
use App\Events\NewValue;
use App\Parsers\DataSets\DataSet;

class Importer
{
    protected $dataSet;

    public function load(DataSet $dataSet)
    {
        $this->dataSet = $dataSet;

        return $this;
    }

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

        var_dump("finish ?!? fire value inserted");

        return $this;
    }
}
