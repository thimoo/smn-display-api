<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class CollectionTransformer extends Transformer
{
    use Accessors, AddFilter;

    public $attributes = [
        'code',
        'date',
        'values',
    ];

    public function filterValues($model, $key)
    {
        $values = collect(ValueTransformer::getAll($model->$key));
        $c = $values->count();
        $values->transform(function($item, $key) use ($c) {
            $item->index = $c - $key;
            return $item;
        });
        return $values->toArray();
    }
}
