<?php

namespace App\Transformers;

use Carbon\Carbon;
use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class ValueTransformer extends Transformer
{
    use Accessors, AddFilter;

    /**
     * List of json attributes
     * 
     * @var array
     */
    public $attributes = [
        'date',
        'value',
        'tag',
        'index',
    ];

    /**
     * Filter the date to w3c format
     * 
     * @param  mixed  $model the source model
     * @param  string $key   the attribute name
     * @return string        the date in w3c format
     */
    public function filterDate($model, $key)
    {
        return (new Carbon($model->$key))->toW3cString();
    }

    /**
     * Filter the value attribute. Cast in float
     * 
     * @param  mixed  $model the result object
     * @param  string $key   the attribute name
     * @return float
     */
    public function filterValue($model, $key)
    {
        return (float) $model->$key;
    }
}
