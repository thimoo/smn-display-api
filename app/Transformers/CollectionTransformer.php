<?php

namespace App\Transformers;

use App\Data;
use Carbon\Carbon;
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

    /**
     * List of renamed attributes
     *
     * @var array
     */
    public $morph = [
        'href' => '$href',
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
     * Filter the values attributes. Transform the collection with
     * ValueTransformer and add indexes
     *
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

    /**
     * Add a link to refresh the collection
     *
     * @param mixed $object the result object
     * @param mixed $model  the collection model
     */
    public function addHref($object, $model)
    {
        $profile_code = $model->profile;
        $data_code = $model->code;
        return Data::fullUri($profile_code, $data_code, 'collections');
    }
}
