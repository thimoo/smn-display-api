<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class SingleValueTransformer extends Transformer
{
    use Accessors, AddFilter;

    public $attributes = [
        'data_code',
        'date',
        'value',
        'tag',
        'agregator',
        'original',
    ];

    public $morph = [
        'data_code' => 'code',
    ];

    public function filterValue($model, $key)
    {
        return (float) $model->$key;
    }
}
