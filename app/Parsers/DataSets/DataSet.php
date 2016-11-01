<?php

namespace App\Parsers\DataSets;

class DataSet
{
    /**
     * Stores the data name
     * 
     * @var array
     */
    protected $data = []; // ['tre200s0', 'sre000z0', 'rre150z0', ...]

    /**
     * Stores the profiles name
     * 
     * @var array
     */
    protected $profiles = []; // ['cha', 'jun', 'alt', ...]

    /**
     * Stores an array of array of values the first
     * level represent profiles and the second represent
     * data
     * 
     * @var array
     */
    protected $content = []; // [ [ 12, null, 56, ... ], [ ... ], ... ]

    /**
     * Stores the datetime of the set
     * 
     * @var Carbon\Carbon
     */
    protected $datetime;

    /**
     * Stores the cursor position for the data
     * 
     * @var integer
     */
    private static $DATA_CURSOR = 0;

    /**
     * Stores the cursor position for the profile
     * 
     * @var integer
     */
    private static $PROFILE_CURSOR = 1;

    /**
     * Stores the current cursor positions
     * 
     * @var array
     */
    private $cursors = [0, 0];

    /**
     * Return the datetime of the DataSet
     * 
     * @return Carbon\Carbon the set datetime
     */
    public final function datetime()
    {
        return $this->datetime;
    }

    /**
     * Return the next value in the set
     * 
     * @return array of profile, data, value, time
     */
    public final function getNextValue()
    {
        $p = $this->getTheProfile();
        $d = $this->getTheData();
        $v = $this->getTheValue();
        $t = $this->datetime;
        
        $this->setNextCursors();

        return [$p, $d, $v, $t];
    }

    /**
     * Return if the set has a next value
     * 
     * @return boolean
     */
    public final function hasNextValue()
    {
        return $this->hasNextProfile() && $this->hasNextData();
    }

    /**
     * Return the number of values in the set
     * 
     * @return int number of values
     */
    public final function count()
    {
        $count = 0;
        foreach ($this->content as $array) {
            $count += count($array);
        }
        return $count;
    }

    /**
     * Return the number of data
     * 
     * @return int number of data
     */
    public final function getNumberHeader()
    {
        return count($this->data);
    }

    /**
     * Return the number of profiles
     * 
     * @return int number of profiles
     */
    public final function getNumberProfile()
    {
        return count($this->profiles);
    }

    /**
     * Set the datetime for the DataSet
     * 
     * @param Carbon\Carbon $datetime the new datetime
     * @return DataSet $this
     */
    public final function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Set the data attribute
     * 
     * @param array $data data
     */
    public final function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the profiles attribute
     * 
     * @param array $profiles profiles
     */
    public final function setProfiles(array $profiles)
    {
        $this->profiles = $profiles;

        return $this;
    }

    /**
     * Set the content array
     * 
     * @param array $content array of arrays
     */
    public final function setContent(array $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Display the complete Data set
     * 
     * @return void
     */
    public final function display()
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

    /**
     * Populate a Data set with the given parameters
     * All values are set to null. This method is used
     * to create a empty Data set for populate the database
     * with no-data values
     * 
     * @param  Carbon\Carbon   $date       the data set time
     * @param  Collection      $profiles   a collection of Profile
     * @param  Collection      $data       a collection of Data
     * @return DataSet                     $this
     */
    public final function populate($date, $profiles, $data)
    {
        $this->setDatetime($date)
            ->transformProfiles($profiles)
            ->transformData($data)
            ->generateEmptyContent();

        return $this;
    }

    /**
     * Transform a collection of Profile in an array
     * that correspond to the internal format
     * 
     * @param  collection   $profiles   a collection of Profile
     * @return void
     */
    private function transformProfiles($profiles)
    {
        $array = array_column($profiles->toArray(), 'stn_code');
        $this->setProfiles($array);
    }

    /**
     * Transform a collection of Data in an array
     * that corrrespond to the internal format
     * 
     * @param  collection  $data   a collection of Data
     * @return void
     */
    private function transformData($data)
    {
        $array = array_column($data->toArray(), 'smn_code');
        $this->setData($array);
    }

    /**
     * Generate the empty content with null value 
     * based on the stored profiles and data
     * 
     * @return void
     */
    private function generateEmptyContent()
    {
        $this->content = [];

        foreach ($this->profiles as $profile) {
            $values = array_fill(0, $this->getNumberHeader(), null);

            $this->content[] = $values;
        }
    }

    /**
     * Set the value for the data cursor and the
     * profile cursor to browse all the content
     *
     * @return void
     */
    private function setNextCursors()
    {
        $this->setNextDataCursor();

        if (! $this->hasNextData() && $this->hasNextProfile())
        {
            $this->setNextProfileCursor();
            $this->resetDataCursor();
        }
    }

    /**
     * Get the current profile based on the current
     * cursors state
     * 
     * @return string the stn_code of the profile
     */
    private function getTheProfile()
    {
        return $this->profiles[$this->getProfileCursor()];
    }

    /**
     * Get the current data based on the current
     * cursors state
     * 
     * @return string the smn_code of the data
     */
    private function getTheData()
    {
        return $this->data[$this->getDataCursor()];
    }

    /**
     * Get the current value based on the current
     * cursors state
     * 
     * @return float or null if no value
     */
    private function getTheValue()
    {
        return $this->content[$this->getProfileCursor()][$this->getDataCursor()];
    }

    /**
     * Return if a next profile is present in the
     * data set
     * 
     * @return boolean
     */
    private function hasNextProfile()
    {
        return $this->getProfileCursor() < count($this->profiles);
    }

    /**
     * Return if a next data is present in the
     * data set
     * 
     * @return boolean
     */
    private function hasNextData()
    {
        return $this->getDataCursor() < count($this->data);
    }

    /**
     * Return the current index of the profile cursor
     * 
     * @return int current position
     */
    private function getProfileCursor()
    {
        return $this->cursors[self::$PROFILE_CURSOR];
    }

    /**
     * Return the current index of the data cursor
     * 
     * @return int current position
     */
    private function getDataCursor()
    {
        return $this->cursors[self::$DATA_CURSOR];
    }

    /**
     * Increment the profile cursor
     *
     * @return void
     */
    private function setNextProfileCursor()
    {
        $this->cursors[self::$PROFILE_CURSOR]++;
    }

    /**
     * Increment the data cursor
     *
     * @return void
     */
    private function setNextDataCursor()
    {
        $this->cursors[self::$DATA_CURSOR]++;
    }

    /**
     * Reset the data cursor to zero
     * 
     * @return void
     */
    private function resetDataCursor()
    {
        $this->cursors[self::$DATA_CURSOR] = 0;
    }

    /**
     * Reset the profile cursor to zero
     * 
     * @return void
     */
    private function resetProfileCursor()
    {
        $this->cursors[self::$PROFILE_CURSOR] = 0;
    }

    /**
     * Reset all cursors to zero
     * 
     * @return void
     */
    public function resetCursors()
    {
        $this->cursors = [0, 0];
    }
}
