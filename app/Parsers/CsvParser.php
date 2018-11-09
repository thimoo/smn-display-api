<?php

namespace App\Parsers;

use Carbon\Carbon;

class CsvParser extends Parser
{
    /**
     * Stores the current state of validation
     *
     * @var boolean
     */
    protected $validFormat = true;

    /**
     * Stores the decomposed content into lines
     *
     * @var array
     */
    protected $lines = [];

    /**
     * Stores found headers
     *
     * @var array
     */
    protected $header = [];

    /**
     * Stores found profiles
     *
     * @var array
     */
    protected $profiles = [];

    /**
     * Stores an array of arrays of values
     *
     * @var array
     */
    protected $values = [];

    /**
     * Stores the datetime of the content
     *
     * @var Carbon\Carbon
     */
    protected $datetime;

    /**
     * Stores the recentDate of the content
     *
     * @var Carbon\Carbon
     */
    protected $recentDate=null;

    /**
     * Decompose the content in headers and lines
     * and populate the DataSet
     *
     * @return CsvParser $this
     */
    public function parse()
    {
        $this->decompose()
             ->cleanUp()
             ->parseHeader()
             ->parseLines()
             ->populateDataSet();

        return $this;
    }

    /**
     * Return if the content have been parsed correctly
     *
     * @return bool validation
     */
    public function validateFormat() : bool
    {
        return $this->validFormat;
    }

    /**
     * Explode the content into lines and store it in lines
     * property
     *
     * @return CsvParser $this
     */
    protected function decompose()
    {
        $this->lines = explode("\n", $this->content);

        return $this;
    }

    /**
     * Clean the lines array by removing empty lines
     * and the title line
     *
     * @return CsvParser $this
     */
    protected function cleanUp()
    {
        $this->lines = array_filter($this->lines, function ($value) {
            if ($value && strlen($value) > 1) return $value;
        });

        $this->removeTitle();

        return $this;
    }

    /**
     * Check if the first line is the title line,
     * if not the validation failed. Else, the first
     * line is removed
     *
     * @return CsvParser $this
     */
    protected function removeTitle()
    {
        if ($this->checkTitle())
        {
            $this->lines = array_slice($this->lines, 1);
        }
        else $this->validFormat = false;

        return $this;
    }

    /**
     * Check if the first line contains the word "MeteoSuisse"
     *
     * @return bool true if contains
     */
    protected function checkTitle()
    {
        $title = $this->lines[0];
        return preg_match("/MeteoSuisse/", $title) > 0;
    }

    /**
     * Check if data headers are present, if not the validation
     * failed. Else, exctract headers and update lines array by
     * removing the first line of headers
     *
     * @return CsvParser $this
     */
    protected function parseHeader()
    {
        if ($this->checkDataHeader())
        {
            $this->header = $this->formatHeader($this->lines[0]);
            $this->lines = array_slice($this->lines, 2);
        }
        else $this->validFormat = false;

        return $this;
    }

    /**
     * Exctract the headers from the line string by exploding the
     * string with ; and remove the two firsts
     *
     * @param  string $header the string containing the headers
     * @return array          an array of headers
     */
    protected function formatHeader(string $header)
    {
        $header = explode(';', $header);
        return array_slice($header, 2);
    }

    /**
     * Check if the first line contains the header identifier
     *
     * @return bool true if contains "/(stn|time)/"
     */
    protected function checkDataHeader()
    {
        $title = $this->lines[0];
        return preg_match("/(stn|time)/", $title) > 0;
    }

    /**
     * Parse all lines present in the lines array and decompose
     * it to populate the profiles array and the values array
     *
     * @return CsvParser $this
     */
    protected function parseLines()
    {
        foreach ($this->lines as $line) {
            // Explode the line and grab the first item
            // to populate the profiles array with the
            // normalized data
            $values = explode(';', $line);
            $profile = $this->normalizeData($values[0]);
            $this->profiles[] = $profile;

            // Grab the datetime of the current profile
            $datetime = $values[1];
            $this->udpateDatetime($datetime);

            // Remove the two items [m/s] (pos 7 and 8) (fkl010z0 and fkl010z1)
            unset($values[6]);
            unset($values[7]);

            // Remove the two first items and parse
            // the values
            $values = array_slice($values, 2);
            $this->values[] = $this->parseValues($values);
        }

        return $this;
    }

    /**
     * Transform an array of string into an array of floats
     * and null values
     *
     * @param  array  $values array of string
     * @return array          normalized values
     */
    protected function parseValues(array $values)
    {
        return array_map(function ($value) {
            if (strcmp('-', $value) == 0) return null;
            else return (float) $value;
        }, $values);
    }

    /**
     * Transform the string datetime into a Carbon date
     * If the datetime is null, set it with the current date.
     * If the datetime is different that the datetime parameter,
     * then the validation failed
     *
     * @param  string $datetime value datetime
     * @return void
     */
    protected function udpateDatetime($datetime)
    {
        $date = Carbon::createFromFormat('YmdHi', $datetime);

        $date = $this->mostRecentDate($date);

        if ($this->datetime == null) $this->datetime = $date;
        // If the current datetime is different that the
        // stores datetime, then the CSV is not conform with
        // the format
        elseif ($this->datetime->diffInMinutes($date) != 0) $this->validFormat = false;
    }

    /**
     * Populate the DataSet with the parsed data
     *
     * @return CsvParser $this
     */
    protected function populateDataSet()
    {
        $this->dataSet->setData($this->header)
            ->setProfiles($this->profiles)
            ->setContent($this->values)
            ->setDatetime($this->datetime);

        return $this;
    }

    /**
     * Compares the dates to get the most recent date
     *
     * @param  string $datetime value datetime
     * @return void
     */
    protected function mostRecentDate($datetime)
    {
        if ($this->recentDate == null) $this->recentDate = $datetime;
        if ($this->recentDate->lessThan($datetime)) $this->recentDate = $datetime;

        return $this->recentDate;
    }

    /**
     * Remove the space before and after and transform to
     * lower case the string
     *
     * @param  string $value string to normalize
     * @return string        normalized string
     */
    protected function normalizeData(string $value)
    {
        return trim(strtolower($value));
    }
}
