<?php

namespace App\Console\Commands;

use App\Data;
use App\Value;
use App\Profile;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FakeGraph extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:graph 
                            {profile : The stn_code of the profile}
                            {data : The code of the data}
                            {noData? : The number of no-data}
                            {min? : The minimum value for starting the graph}
                            {max? : The maximum value for starting the graph}
                            {--B|bar : Set the graph as a bar graph}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a fake graphic for the given profile and data';

    /**
     * Define the default number of continious no-data value in the graph
     * 
     * @var integer
     */
    protected $noDataNumber = 6;

    /**
     * Defines the minimum value for starting the graph
     * 
     * @var integer
     */
    protected $min = -20;

    /**
     * Defines the maximum value for starting the graph
     * 
     * @var integer
     */
    protected $max = 30;

    /**
     * Defines if values is for a line graph or a bar graph
     * 
     * @var boolean
     */
    protected $barMode = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $stnCode = $this->argument('profile');
        $code = $this->argument('data');
        
        $this->min = ($this->argument('min') === null) ? $this->min : (int) $this->argument('min');
        $this->max = ($this->argument('max') === null) ? $this->max : (int) $this->argument('max');

        $this->noDataNumber = ($this->argument('noData') === null) ? $this->noDataNumber : (int) $this->argument('noData');

        var_dump($this->min);
        var_dump($this->max);
        var_dump($this->noDataNumber);

        if ($this->option('bar'))
        {
            $this->min = 0;
            $this->max = 1;
            $this->barMode = true;
        }

        try
        {
            $collection = collect();
            $profile = Profile::findOrFail($stnCode);
            $data = Data::findOrFail($code);

            Value::where('profile_stn_code', $stnCode)
                    ->where('data_code', $code)
                    ->delete();
            
            $date = new Carbon();
            $date->subSeconds($date->second);
            $date->subMinutes($date->minute % 10);
            $date->subHours(24);

            for ($i=0; $i < 144; $i++) { 

                $date = new Carbon($date);

                $last = $collection->get($i-1);
                list($value, $tag) = $this->getRandomValue($last);

                $v = new Value([
                    'data_code' => $code,
                    'profile_stn_code' => $stnCode,
                    'date' => $date,
                    'value' => $value,
                    'tag' => $tag,
                ]);
                $collection[] = $v;

                $date->addMinutes(10);

            }

            foreach ($collection as $value) {
                $value->save();
            }

        }
        catch (Exception $e)
        {
            $this->error('Something went wrong!');
        }
    }

    /**
     * Use to generate random and uniform values
     * 
     * @return array the value and the tag
     */
    protected function getRandomValue(Value $prev = null)
    {
        $tab = [];

        if ($prev != null)
        {
            $diff = rand(-4, 4) / 10;
            $nextValue = $prev->value + $diff;

            if ($this->barMode)
            {
                $nextValue = max($this->min, $nextValue);
                $nextValue = min($this->max, $nextValue);

                if (rand(0, 99) >= 95)
                {
                    $nextValue = 0;
                }
            }

            switch ($prev->tag) {
                case Value::NODATA:
                    if ($this->noDataNumber > 0) {
                        $tab = [0, Value::NODATA];
                        $this->noDataNumber--;
                    }
                    else 
                    {
                        $nextValue = rand($this->min, $this->max);
                        $tab = [$nextValue, Value::ORIGINAL];
                    }
                    break;

                default:
                    if ($this->noDataNumber > 0 && rand(0, 99) >= 95) 
                    {
                        $tab = [0, Value::NODATA];
                        $this->noDataNumber--;
                    }
                    else 
                    {
                        $tab = [$nextValue, Value::ORIGINAL];
                    }
                    break;
            }
        }
        else 
        {
            $tab = [rand($this->min, $this->max), Value::ORIGINAL];
        }

        return $tab;
    }
}
