<?php

namespace App\Http\Controllers\Agregators;

use App\Value;

class Agregator
{
    /**
     * Stores the agregator name
     *
     * @var null
     */
    protected static $agregator = null;

    /**
     * List of agregator function available
     *
     * @var array
     */
    protected static $agregators = ['min', 'max', 'sum'];

    /**
     * Agregate the collection for the given value
     *
     * @param  Value  $value the reference value
     * @return Value         a new fictive value with the result
     */
    public static function agregate(Value $value)
    {
        self::$agregator = $value->agregator;
        if (! self::$agregator) return null;
        if (! in_array(self::$agregator, self::$agregators)) return null;
        $new = self::{self::$agregator}($value);
        $new->agregator = self::$agregator;
        return $new;
    }

    /**
     * Get the min value in the collection
     *
     * @param  Value  $value the reference value
     * @return float
     */
    public static function min(Value $value)
    {
        return $value->getMinValue();
    }

    /**
     * Get the max value in the collection
     *
     * @param  Value  $value the reference value
     * @return float
     */
    public static function max(Value $value)
    {
        return $value->getMaxValue();
    }

    /**
     * Get the sum of all value present in the collection
     *
     * @param  Value  $value the reference value
     * @return float
     */
    public static function sum(Value $value)
    {
        $sum = $value->getSumString();
        $value->value = number_format ($sum , 1);
        return $value;
    }
}
