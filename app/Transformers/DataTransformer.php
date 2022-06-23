<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class DataTransformer extends Transformer
{
    use Accessors, AddFilter;

    /**
     * List of json attributes
     *
     * @var array
     */
    public $attributes = [
        'code',
        'agregator',
        'value',
        'date',
        'tag',
        'original',
    ];
}
