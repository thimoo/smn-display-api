<?php

namespace App\Listeners;

use App\Data;
use App\Value;
use App\Profile;
use App\Display;
use App\Events\CheckConstraints;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckDisplay
{
    /**
     * Stores the profile to work with
     *
     * @var App\Profile
     */
    public $profile;

    /**
     * Stores the data to work with
     *
     * @var App\Data
     */
    public $data;

    /**
     * Stores the collection of values between the stored
     * data and the stored profile
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $values;

    /**
     * Store the first value of the collection, the latest
     * in time
     *
     * @var App\Value
     */
    protected $first;

    /**
     * Handle the event.
     *
     * @param  CheckConstraints  $event
     * @return void
     */
    public function handle(CheckConstraints $event)
    {
      $output = new \Symfony\Component\Console\Output\ConsoleOutput();
      $output->writeln("<info>first".count($event->values)."</info>");

      // Unpack stored data
      $this->profile = $event->profile;
      $this->data = $event->data;
      $this->values = $event->values;

      if(count($event->values)>0){

        $time=$this->getValueTime();

        foreach ($this->values as $value) {
          if($value->date === $time)
          {
            $this->first = $value;
            if ($this->first->isOriginal())
            {
              $this->originalValue();
            }
            else
            {
              $this->noOriginalValue();
            }
          }
        }
      }
    }
    /**
     * If an original value is present, then show
     * the data and check if the collection can be
     * show
     *
     * @return void
     */
    protected function originalValue()
    {
        Display::showData($this->data, $this->profile);

        $noData = Value::countNoDataValue($this->values);

        $maxNoDataConstant = config('constants.max_number_no_data_to_show_collection');
        if ($noData <= $maxNoDataConstant)
        {
            Display::showCollection($this->data, $this->profile);
        }
    }

    /**
     * If no originial value is present, then check
     * if the value is tagged as no-data. If yes, then
     * check if they are more than 36 continious no-data
     * value and hide the data and the collection
     *
     * @return void
     */
    protected function noOriginalValue()
    {
        if ($this->first->isNoData())
        {
            $noData = Value::countLastNoData($this->values);

            $maxNoDataConstant = config('constants.max_number_no_data_to_hide_data');
            if ($noData > $maxNoDataConstant)
            {
                Display::hideData($this->data, $this->profile);
                Display::hideCollection($this->data, $this->profile);
            }
        }
    }

    protected function getValueTime()
    {
      $time =0;
      foreach ($this->values as $value) {
        if($time<$value->date)
        {
          $time=$value->date;
        }
      }
      return $time;
    }
}
