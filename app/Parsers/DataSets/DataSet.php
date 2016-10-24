<?

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

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function setProfiles(array $profiles)
    {
        $this->profiles = $profiles;
    }

    public function setContent(array $content)
    {
        $this->content = $content;
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
        $this->setNextProfileCursor();
        $this->setNextDataCursor();

        if (! $this->hasNextData() && $this->hasNextProfile())
        {
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

}
