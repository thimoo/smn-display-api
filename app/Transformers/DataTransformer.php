<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class DataTransformer extends Transformer
{
    use Accessors, AddFilter;

    public $attributes = [
        'code',
        'agregator',
        'value',
        'date',
        'tag',
        'original',
    ];
}
