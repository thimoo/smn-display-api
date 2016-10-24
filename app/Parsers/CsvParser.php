<?

namespace App\Parsers;

use Carbon\Carbon;


class CsvParser extends Parser
{
    protected $validFormat = true;

    protected $lines;

    protected $header = [];

    protected $profiles = [];

    protected $values = [];

    protected $datetime;

    public function parse()
    {
        $this->decompose()
             ->cleanUp()
             ->parseHeader()
             ->parseLines()
             ->populateDataSet();

        return $this;
    }

    public function validateFormat() : bool
    {
        return $this->validFormat;
    }

    protected function decompose()
    {
        $this->lines = explode("\n", $this->content);

        return $this;
    }

    protected function cleanUp()
    {
        $this->lines = array_filter($this->lines, function ($value) {
            if ($value && strlen($value) > 1) return $value;
        });

        $this->removeTitle();

        return $this;
    }

    protected function removeTitle()
    {
        if ($this->checkTitle())
            $this->lines = array_slice($this->lines, 1);
        else
            $this->validFormat = false;

        return $this;
    }

    protected function checkTitle()
    {
        $title = $this->lines[0];

        return preg_match("/MeteoSuisse/", $title) > 0;
    }

    protected function parseHeader()
    {
        if ($this->checkDataHeader())
        {
            $this->header = $this->formatHeader($this->lines[0]);
            $this->lines = array_slice($this->lines, 1);
        }

        return $this;
    }

    protected function formatHeader(string $header)
    {
        $header = explode('|', $header);
        return array_slice($header, 2);
    }

    protected function checkDataHeader()
    {
        $title = $this->lines[0];

        return preg_match("/(stn|time)/", $title) > 0;
    }

    protected function parseLines()
    {
        foreach ($this->lines as $line) {
            // explode line
            $values = explode('|', $line);
            // grab 0 -> add to profiles[]
            $profile = $this->normalizeData($values[0]);
            $this->profiles[] = $profile;
            // grab 0 -> update datetime
            $datetime = $values[1];
            $this->udpateDatetime($datetime);
            // push array to value
            $values = array_slice($values, 2);
            $this->values[] = $this->parseValues($values);
        }

        return $this;
    }

    protected function parseValues(array $values)
    {
        return array_map(function ($value) {
            if (strcmp('-', $value) == 0) return null;
            else return (float) $value;
        }, $values);
    }

    protected function udpateDatetime($datetime)
    {
        $date = Carbon::createFromFormat('YmdHi', $datetime);

        if ($this->datetime == null)
            $this->datetime = $date;
        elseif ($this->datetime->lt($date)) {
            $this->datetime = $date;
        }
    }

    protected function populateDataSet()
    {
        $this->dataSet->setData($this->header);
        $this->dataSet->setProfiles($this->profiles);
        $this->dataSet->setContent($this->values);
        $this->dataSet->setDatetime($this->datetime);

        return $this;
    }

    protected function normalizeData($value)
    {
        return trim(strtolower($value));
    }
}
