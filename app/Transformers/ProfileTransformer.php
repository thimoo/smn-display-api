<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class ProfileTransformer extends Transformer
{
    use Accessors, AddFilter;

    /**
     * List of json attributes
     * 
     * @var array
     */
    public $attributes = [
        'stn_code', 
        'altitude', 
        'infos', 
        'last_update', 
        'last_time_online', 
        'online',
        'data_display',
        'collections_display',
    ];

    /**
     * List of renamed attributes
     * 
     * @var array
     */
    public $morph = [
        'stn_code' => 'stnCode',
        'last_update' => 'lastUpdate',
        'last_time_online' => 'lastTimeOnline',
        'online' => 'isOnline',
        'data_display' => 'data',
        'collections_display' => 'collections',
        'href' => '$href',
    ];

    /**
     * Filter the online attribute. Cast in boolean
     * 
     * @param  mixed  $model the result object
     * @param  string $key   the attribute name
     * @return boolean
     */
    public function filterIsOnline($model, $key)
    {
        return (bool) $model->$key;
    }

    /**
     * Filter the data attribute. Transform the list of data
     * in array
     * 
     * @param  mixed  $model the result object
     * @param  string $key   the attribute name
     * @return array
     */
    public function filterData($model, $key)
    {
        return ShortDataTransformer::getAll($model->$key);
    }

    /**
     * Filter the collections attribute. Transform the list of collection
     * in array
     * 
     * @param  mixed  $model the result object
     * @param  string $key   the attribute name
     * @return array
     */
    public function filterCollections($model, $key)
    {
        return ShortCollectionTransformer::getAll($model->$key);
    }

    /**
     * Add a new attribute to the result object.
     * Create a link to refresh the profile
     * 
     * @param mixed $object the result object
     * @param mixed $model  the source model
     * @return string
     */
    public function addHref($object, $model)
    {
        return $model->fullProfileUri($model->stn_code);
    }
}
