<?php

namespace App\Parsers;

use App\Parsers\DataSets\DataSet;

abstract class Parser
{
    /**
     * Store the string content to parse
     *
     * @var string
     */
    protected $content;

    /**
     * Store a new empty DataSet
     *
     * @var App\Parsers\DataSets\DataSet
     */
    protected $dataSet;

    /**
     * Construct the parser with a new DataSet and
     * the string content
     *
     * @param string $content the CSV content
     */
    function __construct(string $content)
    {
        $this->dataSet = new DataSet;
        $this->content = $content;
    }

    /**
     * Transform the string content given in a
     * DataSet
     *
     * @return Parser $this
     */
    abstract public function parse();

    /**
     * Validate the result of the DataSet or the
     * content string given
     * given
     *
     * @return bool if the validation pass
     */
    abstract public function validateFormat() : bool;

    /**
     * Return the DataSet
     *
     * @return App\Parsers\DataSets\DataSet the DataSet parsed
     */
    public function get()
    {
        return $this->dataSet;
    }
}
