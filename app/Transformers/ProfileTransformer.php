<?php

namespace App\Transformers;

use Carbon\Carbon;
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
     * Filter the infos object
     *
     * @param  mixed  $model the source model
     * @param  string $key   the attrivute name
     * @return object        the pased infos object
     */
    public function filterInfos($model, $key)
    {
        $infos = $model->$key;

        if (isset($infos->altitude))
        {
            // Convert the altitude attribute to integer
            $infos->altitude = (int) $infos->altitude;
        }

        if (isset($infos->altitudeTowz))
        {
            // Convert the altitude attribute to integer
            $infos->altitudeTowz = (int) $infos->altitudeTowz;
        }

        return $infos;
    }

    /**
     * Filter the last update datetime to w3c format
     *
     * @param  mixed  $model the source model
     * @param  string $key   the attribute name
     * @return string        the date in w3c format
     */
    public function filterLastUpdate($model, $key)
    {
        return (new Carbon($model->$key))->toW3cString();
    }

    /**
     * Filter the last time online datetime to w3c format
     *
     * @param  mixed  $model the source model
     * @param  string $key   the attribute name
     * @return string        the date in w3c format
     */
    public function filterLastTimeOnline($model, $key)
    {
        if ($model->$key == null) return null;
        else return (new Carbon($model->$key))->toW3cString();
    }

    /**
     * Filter the online attribute. Cast in boolean
     *
     * @param  mixed  $model the source model
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
