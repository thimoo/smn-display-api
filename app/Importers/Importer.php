<?php

namespace App\Importers;

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
            var_dump($profile, $data, $value, $time);
        }

        var_dump($this->dataSet->count());

        return $this;
    }
}