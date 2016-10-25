<?

namespace App\Parsers;

use App\Parsers\DataSets\DataSet;

abstract class Parser
{

    protected $content;

    protected $dataSet;

    function __construct($content)
    {
        $this->dataSet = new DataSet;
        $this->content = $content;
    }

    abstract public function parse();
    
    abstract public function validateFormat() : bool;

    public function get()
    {
        return $this->dataSet;
    }
}