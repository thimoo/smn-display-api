<?php

namespace App\Http\Controllers\Agregators;

use App\Value;

class Agregator
{
    protected static $agregator = null;

    protected static $agregators = ['min', 'max', 'sum'];

    public static function agregate(Value $value)
    {
        self::$agregator = $value->agregator;
        if (! self::$agregator) return null;
        if (! in_array(self::$agregator, self::$agregators)) return null;
        $new = self::{self::$agregator}($value);
        $new->agregator = self::$agregator;
        return $new;
    }

    public static function min(Value $value)
    {
        return $value->getMinValue();
    }

    public static function max(Value $value)
    {
        return $value->getMaxValue();
    }

    public static function sum(Value $value)
    {
        $sum = $value->getSumString();
        $value->value = $sum;
        return $value;
    }
}
