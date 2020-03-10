<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Value extends Model
{
    /**
     * Value of original tag in database
     */
    const ORIGINAL = 'original';

    /**
     * Value of substituted tag in database
     */
    const SUBSTITUTED = 'substituted';

    /**
     * Value of smoothed tag in database
     */
    const SMOOTHED = 'smoothed';

    /**
     * Value of no-data tag in database
     */
    const NODATA = 'no-data';

    /**
     * Indicate the primary key fields
     *
     * @var array
     */
    protected $primaryKey = ['data_code', 'profile_stn_code', 'date'];

    /**
     * The table name in database
     *
     * @var string
     */
    protected $table = "values";

    /**
     * Indicate if the primary key is an incermetal int
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data_code',
        'profile_stn_code',
      	'date',
      	'value',
      	'tag',
    ];

    /**
     * Get the profile that owns the value
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'stn_code', 'profile_stn_code');
    }

    /**
     * Get the data that owns the value
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function data()
    {
        return $this->hasOne(Data::class, 'code', 'data_code');
    }

    /**
     * Retreive the collection of values that correspond to
     * the given profile and the given data
     *
     * @param  App\Profile  $p
     * @param  App\Data     $d
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCollectionFor(Profile $p, Data $d)
    {
        return self::getCollectionWith($p->stn_code, $d->code);
    }

    /**
     * Retreive the collection of values that correspond to
     * the given profile and the given data
     *
     * @param  string $stn_code the stn_code
     * @param  string $code     the internal data code
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCollectionWith($stn_code, $code)
    {
        return Value::where('profile_stn_code', $stn_code)
                    ->where('data_code', $code)
                    ->orderBy('date', 'desc')
                    ->get();
    }

    /**
     * Retreive the last value that correspond to the given
     * profile and the given data
     *
     * @param  App\Profile  $p
     * @param  App\Data     $d
     * @return App\Value
     */
    public static function getLastValueFor(Profile $p, Data $d)
    {
        return Value::where('profile_stn_code', $p->stn_code)
                    ->where('data_code', $d->code)
                    ->orderBy('date', 'desc')
                    ->first();
    }

    /**
     * Return the minimum value in a collection between the profile
     * and the data
     *
     * @return string the minimum value
     */
    public function getMinString()
    {
        return Value::where('profile_stn_code', $this->profile_stn_code)
                    ->where('data_code', $this->data_code)
                    ->where('tag', '<>', self::NODATA)
                    ->min('value');
    }

    /**
     * Return the minimum value model in a collection
     *
     * @return App\Value the minimum value
     */
    public function getMinValue()
    {
        return Value::where('profile_stn_code', $this->profile_stn_code)
                    ->where('data_code', $this->data_code)
                    ->where('tag', '<>', self::NODATA)
                    ->where('value', $this->getMinString())
                    ->first();
    }

    /**
     * Return the maximum value in a collection between the profile
     * and the data
     *
     * @return string the maximum value
     */
    public function getMaxString()
    {
        return Value::where('profile_stn_code', $this->profile_stn_code)
                    ->where('data_code', $this->data_code)
                    ->where('tag', '<>', self::NODATA)
                    ->max('value');
    }

    /**
     * Return the maximum value model in a collection
     *
     * @return App\Value the maximum value
     */
    public function getMaxValue()
    {
        return Value::where('profile_stn_code', $this->profile_stn_code)
                    ->where('data_code', $this->data_code)
                    ->where('tag', '<>', self::NODATA)
                    ->where('value', $this->getMaxString())
                    ->first();
    }

    /**
     * Return the sum value in a collection between the profile
     * and the data
     *
     * @return string the sum value
     */
    public function getSumString()
    {
        return Value::where('profile_stn_code', $this->profile_stn_code)
                    ->where('data_code', $this->data_code)
                    ->sum('value');
    }

    /**
     * Save a value as original
     *
     * @param  App\Value    $value
     * @return App\Value
     */
    public static function insertAsOriginal(Value $value)
    {
        $valueUpdate = Value::where('profile_stn_code', $value->profile_stn_code)
        ->where('data_code', $value->data_code)
        ->where('date', $value->date);

        if($valueUpdate->count()){
          $value = $valueUpdate->first();
        }

        $value->tag = self::ORIGINAL;
        $value->save();
    }

    /**
     * Save a new value as substituted and copy the
     * value of the old one
     *
     * @param  App\Value    $new
     * @param  App\Value    $old
     * @return void
     */
    public static function insertAsSubstituted(Value $new, Value $old)
    {
      $valueUpdate = Value::where('profile_stn_code', $new->profile_stn_code)
      ->where('data_code', $new->data_code)
      ->where('date', $new->date);

      if($valueUpdate->count()){
        $new = $valueUpdate->first();
      }

      $new->value = $old->value;
      $new->tag = self::SUBSTITUTED;
      $new->save();
    }

    /**
     * Save a value as no-data with a zero value
     *
     * @param  App\Value    $value
     * @return App\Value
     */
    public static function insertAsNoData(Value $value)
    {
      $valueUpdate = Value::where('profile_stn_code', $value->profile_stn_code)
      ->where('data_code', $value->data_code)
      ->where('date', $value->date);

      if($valueUpdate->count()){
        $value = $valueUpdate->first();
      }

        $value->value = 0;
        $value->tag = self::NODATA;
        $value->save();
    }

    /**
     * Get the last substituted continious values tagged
     * as a collection.
     *
     * @param  App\Profile  $profile
     * @param  App\Data     $data
     * @param  Carbon\Carbon   $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSubstitutedLastValues(Profile $profile, Data $data, $date)
    {
        $collection = $profile->values($data)->where('date', '<', $date)->orderBy('date', 'desc')->take(3)->get();
        $bool = true;

        $collection = $collection->filter(function ($value, $key) use (&$bool) {
            if ($bool && ! $value->isSubstituted()) $bool = false;
            if ($bool) return $value;
        });

        return $collection;
    }

    /**
     * Get the last original value present in the collection, null
     * if no original value is present
     *
     * @param  Profile $profile the profile
     * @param  Data    $data    the data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function getLastOriginalValue(Profile $profile, Data $data)
    {
        $collection = $profile->values($data)->orderBy('date', 'desc')->get();
        $bool = false;

        $collection = $collection->filter(function ($value, $key) use (&$bool) {
            if (! $bool && $value->isOriginal()) $bool = true;
            if ($bool) return $value;
        });

        return $collection->first();
    }

    /**
     * Update all values present inside the collection
     * to no-data with a zero value
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $collection
     * @return void
     */
    public static function updateLastValuesToNoData($collection)
    {
        foreach ($collection as $key => &$value) {
            $value->value = 0;
            $value->tag = self::NODATA;
            $value->save();
        }
    }

    /**
     * Get the new value and the last original value
     * and smooth the values in the collection given
     *
     * @param  App\Value                                 $new
     * @param  \Illuminate\Database\Eloquent\Collection  $collection
     * @return void
     */
    public static function smoothSubstitutedValues(Value $new, $collection)
    {
        $count = $collection->count();

        $old = $new->subset()->orderBy('date', 'desc')
            ->offset($count)
            ->take(1)
            ->first();

        foreach ($collection as $key => &$value) {
            $position = $count - $key;
            $value->value = $value->getValueFor($position, $count, $new, $old);
            $value->tag = self::SMOOTHED;
            $value->save();
        }
    }

    /**
     * Calculate the smoothed value for a given position
     * based on the number of elements and the starting value
     * (the old value) and the ending value (the new value)
     *
     * @param  int          $position
     * @param  int          $count      number of element
     * @param  App\Value    $new        the new original value
     * @param  App\Value    $old        the older original value
     * @return float                    rounded with 1 number
     */
    protected function getValueFor(int $position, int $count, Value $new, Value $old)
    {
        return round($old->value
               + ($position * (1 / ($count + 1))
               * ($new->value - $old->value)), 1);
    }

    /**
     * Create a local scope with two where conditions to
     * match the profile and the data of the current object
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubset($query)
    {
        return $query->where('data_code', $this->data_code)
                     ->where('profile_stn_code', $this->profile_stn_code);
    }

    /**
     * Check if the value is tagged as substituted
     *
     * @return boolean
     */
    public function isSubstituted()
    {
        return strcmp($this->tag, self::SUBSTITUTED) === 0;
    }

    /**
     * Check if the value is tagged as original
     *
     * @return boolean
     */
    public function isOriginal()
    {
        return strcmp($this->tag, self::ORIGINAL) === 0;
    }

    /**
     * Check if the value is tagged as no-data
     *
     * @return boolean
     */
    public function isNoData()
    {
        return strcmp($this->tag, self::NODATA) === 0;
    }

    /**
     * Check if the value is tagged as smoothed
     *
     * @return boolean
     */
    public function isSmoothed()
    {
        return strcmp($this->tag, self::SMOOTHED) === 0;
    }

    /**
     * Count the no-data values ​​in the collection rejecting
     * tags which are not no-data
     *
     * @param  \Illuminate\Database\Eloquent\Collection $values the collection
     * @return int                                              number of no-data
     */
    public static function countNoDataValue($values)
    {
        $noDataCollection = $values->reject(function ($value, $key) {
            return ! $value->isNoData();
        });

        return $noDataCollection->count();
    }

    /**
     * Count the last continious no-data value in the given
     * collection of values
     *
     * @param  \Illuminate\Database\Eloquent\Collection $values the collection
     * @return int                                              number of no-data
     */
    public static function countLastNoData($values)
    {
        $bool = true;

        $collection = $values->filter(function ($value, $key) use (&$bool) {
            if ($bool && ! $value->isNoData()) $bool = false;
            if ($bool) return $value;
        });

        return $collection->count();
    }

    /**
     * Set the keys for a save update query.
     * This is a fix for tables with composite keys
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
     protected function setKeysForSaveQuery(Builder $query) {
        if (is_array($this->primaryKey))
        {
            foreach ($this->primaryKey as $pk) {
                $query->where($pk, '=', $this->original[$pk]);
            }
            return $query;
        }
        else
        {
            return parent::setKeysForSaveQuery($query);
        }
    }
}
