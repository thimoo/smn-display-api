<?php

namespace App\Transformers;

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
    ];

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
