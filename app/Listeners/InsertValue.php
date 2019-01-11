<?php

namespace App\Listeners;

use \DB;
use App\Data;
use App\Value;
use App\Profile;
use Carbon\Carbon;
use App\Events\NewValues;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InsertValue
{
    /**
     * The current value to insert
     * The value data_code arrive with the
     * value of smn_code
     *
     * @var App\Value
     */
    protected $value;

    /**
     *
     * @var array
     */
    protected $insertValues;

    /**
     * The precedent value in database for the
     * data and the profile store in current value
     *
     * @var App\Value
     */
    protected $lastValue;

    /**
     * The profile object attach to the current value
     *
     * @var App\Profile
     */
    protected $profile;

    /**
     * The data object attach to the current value
     *
     * @var App\Data
     */
    protected $data;

    /**
     * Handle the event.
     * Retreive all informations required,
     * create the profile if not present,
     * and call the insert process.
     * @param  $event
     * @return void
     */
    public function handle($event)
    {
      foreach ($event->values as $v) {
        // Unpack the value given in the event message
        // and store it in the current object
        $this->value = $v;

        // Get the profile and the data attach to the
        // new value given
        $this->profile = Profile::find($this->value->profile_stn_code);
        $this->data = Data::where('smn_code', $this->value->data_code)->first();

        if ($this->profile === null)
        {
            // If the profile does not already exists,
            // then create the profile with default values
            // and store it in the current object
            $this->profile = Profile::newDefault($this->value->profile_stn_code);
        }

        // If the data is null, then the data is not
        // present in database and the value must be
        // ignored
        if ($this->data)
        {
            // The value created by the importer
            // as the data_code set to the smn_code
            // we must replace the data_code by the
            // right data code
            $this->value->data_code = $this->data->code;

            // Start the process to determine
            // the new value tag and if older
            // values must be updated and how
            $this->insert();
        }
      }

      $this->insertall();
    }

    /**
     * Create request for insert all datas
     * @param int
     * @return void
     */
    protected function insertall()
    {
      $now = Carbon::now();

      $i=0;
      $query="INSERT INTO `values` (`data_code`, `profile_stn_code`, `date`, `value`, `tag`, `created_at`, `updated_at`) VALUES ";

      foreach ($this->insertValues as $value) {
        if($i>15)
        {
          $query.="('".$value->data_code."', '".$value->profile_stn_code."', '".$value->date."', ".$value->value.", '".$value->tag."', '".$now."', '".$now."'),";
          DB::insert(substr($query, 0, -1).";");
          $query="INSERT INTO `values` (`data_code`, `profile_stn_code`, `date`, `value`, `tag`, `created_at`, `updated_at`) VALUES ";
          $i=0;
        }
        else
        {
          $i++;
          $query.="('".$value->data_code."', '".$value->profile_stn_code."', '".$value->date."', ".$value->value.", '".$value->tag."', '".$now."', '".$now."'),";
        }
      }
      if($i!=0)
      {
        DB::insert(substr($query, 0, -1).";");
      }
    }

    /**
     * Starting the insertion process.
     * If a value is present, check all the cases.
     * Else, check all other cases.
     *
     * @return void
     */
    protected function insert()
    {
        if(empty($this->insertValues)){
          $this->lastValue = $this->profile->lastValue($this->data);
        }
        else {
          $this->lastValue = $this->insertValues[sizeof($this->insertValues)-1];
        }

        // A value can be set to zero, we must check
        // than the value is not equal to null
        if ($this->value->value !== null)
        {
            // A value is present in the CSV
            // for the given profile and the given
            // data. Check the last value(s)
            // to determine if a transformation
            // is require
            $this->insertWithValue();
        }
        else
        {
            // A value is not present in the CSV
            // for the given profile and the given
            // data. Check the last value(s) to determine
            // if a substitution or a no-data value
            // must be perform. Check also if a smoothing
            // is required
            $this->insertWithNoValue();
        }

        // Update the last update time on the attach
        // profile
        $this->profile->last_update = $this->value->date;
        $this->profile->save();
    }

    /**
     * Insert the value as original if no last value is
     * present in db. Else call insertWithValueAndLast
     * for check if smoothing is required or not.
     *
     * @return void
     */
    protected function insertWithValue()
    {
        if ($this->lastValue === null)
        {
            // No older value is present with
            // the new one. Insert the new value
            // as original and finish the process
            $this->insertValues[]=Value::insertAsOriginal($this->value);
        }
        else
        {
            // An older value is present with the
            // new one. Check based on last tag
            // what process must be perform
            $this->insertWithValueAndLast();
        }
    }

    /**
     * Check if older values must be smoothed or
     * if the new value can be inserted as original.
     *
     * @return void
     */
    protected function insertWithValueAndLast()
    {
        if (! $this->lastValue->isSubstituted())
        {
            // If the older value is not a substituted
            // value, then it can be a original or no-data
            // In both case we can insert the new data as
            // original and finish the process
            $this->insertValues[]=Value::insertAsOriginal($this->value);
        }
        else
        {
            // The last value is tagged as substituted, so :
            //  - retreive the last 1|2|3 last values
            //  - smooth them with the new value (and the last original before)
            //  - insert the new value as original and finish the process
            $lastSubstitutedValues = Value::getSubstitutedLastValues($this->profile, $this->data);
            Value::smoothSubstitutedValues($this->value, $lastSubstitutedValues);
            $this->insertValues[]=Value::insertAsOriginal($this->value);
        }
    }

    /**
     * If the new value is not present and the
     * last value is also not present, then insert
     * a no-data value. Else, check the last
     * value tags.
     *
     * @return void
     */
    protected function insertWithNoValue()
    {
        if ($this->lastValue === null)
        {
            // If no value is present in the CSV
            // and last value is not present in db,
            // then insert a value zero as no-data
            // and finish the process$
            $this->insertValues[]=Value::insertAsNoData($this->value);
        }
        else
        {
            // If no value is pressent in the CSV
            // but the last value is present in db,
            // then check if substitution can be
            // performe or if old substituted value
            // must be updated to zero value
            // tagged as no-data
            $this->insertWithNoValueButLast();
        }
    }

    /**
     * If the last value is tagged as original,
     * then substitute it. Else, check the last
     * value tags.
     *
     * @return void
     */
    protected function insertWithNoValueButLast()
    {
        if ($this->lastValue->isOriginal())
        {
            // If no data is present in the CSV and
            // the last value is original, then
            // the new value is substituted and
            // the process is finish
            $this->insertValues[]=Value::insertAsSubstituted($this->value, $this->lastValue);
        }
        else
        {
            // If no data is present in the CSV and
            // the last value is substituted or no-data
            $this->insertWithNoValueAndNotOrinial();
        }
    }

    /**
     * If the last value tag is substituted,
     * then check if substitution can be.
     * If not, transform the last 3 substituted
     * values to no-data with the value zero.
     *
     * @return void
     */
    protected function insertWithNoValueAndNotOrinial()
    {
        if ($this->lastValue->isSubstituted())
        {
            // If the last value is substituted,
            // then get the last neighbour substituted
            // values
            $this->tryToUpdateLastNeighbour();
        }
        else
        {
            // If the last value is not original and
            // not substituted, then the last value
            // is tagged as no-data, then the new value
            // is inserted as no-data with a zero value
            // and finish the process
            $this->insertValues[]=Value::insertAsNoData($this->value);
        }
    }

    /**
     * Check if number of latest values substitued
     * correspond to the maximum of neighbour substituted
     * if yes, update the latest value to no data and
     * insert a no-data
     * else, substituted the new data
     *
     * @return void
     */
    protected function tryToUpdateLastNeighbour()
    {
        // Retreive the last substituted neighbour
        // as a collection of Values
        $lastSubstitutedValues = Value::getSubstitutedLastValues($this->profile, $this->data);

        $maxSubstitutedValues = config('constants.max_substituted_values');
        if ($lastSubstitutedValues->count() < $maxSubstitutedValues)
        {
            // If the count of neighbour values is
            // lesser than 3 the substitution can
            // be performed with the new value
            // and finish the process
            $this->insertValues[]=Value::insertAsSubstituted($this->value, $this->lastValue);
        }
        else
        {
            // If the neighbour substituted values is
            // already equals to tree, then we must update
            // this values with a zero value and tag them as
            // no-data
            Value::updateLastValuesToNoData($lastSubstitutedValues);
            // The new value is inserted as a zero value
            // tagged as no-data and finish the process
            Value::insertAsNoData($this->value);
        }
    }
}
