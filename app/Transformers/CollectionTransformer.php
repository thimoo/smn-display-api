<?php

namespace App\Transformers;

use App\Data;
use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class CollectionTransformer extends Transformer
{
    use Accessors, AddFilter;

    /**
     * List of json attributes
     * 
     * @var array
     */
    public $attributes = [
        'code',
        'date',
        'values',
    ];

    public $morph = [
        'href' => '$href',
    ];

    /**
     * Filter the values attributes. Transform the collection with
     * ValueTransformer and add indexes
     * @param  Model  $model an Eloquent model
     * @param  string $key   attribute name
     * @return array         the content of values attribute
     */
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

    public function addHref($object, $model)
    {
        $profile_code = $model->profile;
        $data_code = $model->code;
        return Data::fullUri($profile_code, $data_code, 'collections');
    }
}
