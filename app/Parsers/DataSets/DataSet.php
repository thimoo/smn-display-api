<?php

namespace App\Parsers\DataSets;

class DataSet
{
    protected $data; // ['tre200s0', 'sre000z0', 'rre150z0', 'dkl010z0', ...]

    protected $profiles; // ['cha', 'jun', 'alt', ...]

    protected $content; // [ [ 12, null, 56, ... ], [ ... ], ... ]

    protected $datetime;

    private static $DATA_CURSOR = 0;
    private static $PROFILE_CURSOR = 1;

    private $cursors = [0, 0];

    public function datetime()
    {
        return $this->datetime;
    }

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    public final function getNextValue()
    {
        $p = $this->getTheProfile();
        $d = $this->getTheData();
        $v = $this->getTheValue();
        $t = $this->datetime;
        
        $this->setNextCursors();

        return [$p, $d, $v, $t];
    }

    public final function hasNextValue()
    {
        return $this->hasNextProfile() && $this->hasNextData();
    }

    public function count()
    {
        $count = 0;
        foreach ($this->content as $array) {
            $count += count($array);
        }
        return $count;
    }

    public function getNumberHeader()
    {
        return count($this->data);
    }

    public function getNumberProfile()
    {
        return count($this->profiles);
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function setProfiles(array $profiles)
    {
        $this->profiles = $profiles;

        return $this;
    }

    public function setContent(array $content)
    {
        $this->content = $content;

        return $this;
    }

    public function display()
    {
        echo "\nDump the dataset: \n";
        $nbHeader = $this->getNumberHeader();
        echo "Number of header: $nbHeader\n";
        echo "[" . implode(',', $this->data) . "]\n\n";

        $nbProfile = $this->getNumberProfile();
        echo "Number of profiles: $nbProfile\n";
        echo "[" . implode(',', $this->profiles) . "]\n\n";

        $nbValues = $this->count();
        echo "Number of values: $nbValues\n";

        $this->resetCursors();
        $i = 0;

        while ($this->hasNextValue()) {
            $p = $this->getProfileCursor();
            $d = $this->getDataCursor();

            list($profile, $data, $value, $time) = $this->getNextValue();

            if ($value == null) $value = 'null';

            echo "\n ($p) profile: $profile \t ($d) data:  $data \t = $value \t ($time)";

            $i++;
        }

        echo "\nNumber of iteration: $i\n\n";
    }

    public function populate($date, $profiles, $data)
    {
        $this->setDatetime($date)
            ->transformProfiles($profiles)
            ->transformData($data)
            ->generateEmptyContent();

        return $this;
    }

    private function transformProfiles($profiles)
    {
        $array = array_column($profiles->toArray(), 'stn_code');
        $this->setProfiles($array);
    }

    private function transformData($data)
    {
        $array = array_column($data->toArray(), 'smn_code');
        $this->setData($array);
    }

    private function generateEmptyContent()
    {
        $this->content = [];

        foreach ($this->profiles as $profile) {
            $values = array_fill(0, $this->getNumberHeader(), null);

            $this->content[] = $values;
        }

        $this->resetCursors();
    }

    private function getTheProfile()
    {
        return $this->profiles[$this->getProfileCursor()];
    }

    private function getTheData()
    {
        return $this->data[$this->getDataCursor()];
    }

    private function getTheValue()
    {
        return $this->content[$this->getProfileCursor()][$this->getDataCursor()];
    }

    private function setNextCursors()
    {
        $this->setNextDataCursor();

        if (! $this->hasNextData() && $this->hasNextProfile())
        {
            $this->setNextProfileCursor();
            $this->resetDataCursor();
        }
    }

    private function hasNextProfile()
    {
        return $this->getProfileCursor() < count($this->profiles);
    }

    private function hasNextData()
    {
        return $this->getDataCursor() < count($this->data);
    }

    private function getProfileCursor()
    {
        return $this->cursors[self::$PROFILE_CURSOR];
    }

    private function getDataCursor()
    {
        return $this->cursors[self::$DATA_CURSOR];
    }


    private function setNextProfileCursor()
    {
        $this->cursors[self::$PROFILE_CURSOR]++;
    }

    private function setNextDataCursor()
    {
        $this->cursors[self::$DATA_CURSOR]++;
    }

    private function resetDataCursor()
    {
        $this->cursors[self::$DATA_CURSOR] = 0;
    }

    public function resetCursors()
    {
        $this->cursors = [0, 0];
    }

}
