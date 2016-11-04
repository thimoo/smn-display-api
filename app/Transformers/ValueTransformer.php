<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class ValueTransformer extends Transformer
{
    use Accessors, AddFilter;

    public $attributes = [
        'date',
        'value',
        'tag',
    ];

    public function filterValue($model, $key)
    {
        return (float) $model->$key;
    }
}
