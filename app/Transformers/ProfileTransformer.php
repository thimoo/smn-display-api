<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class ProfileTransformer extends Transformer
{
    use Accessors, AddFilter;

    public $attributes = [
        'stn_code', 
        'altitude', 
        'infos', 
        'last_update', 
        'last_time_online', 
        'online',
        'data',
        'collections',
    ];

    public $morph = [
        'stn_code' => 'stnCode',
        'last_update' => 'lastUpdate',
        'last_time_online' => 'lastTimeOnline',
        'online' => 'isOnline',
        'href' => '$href',
    ];

    public function filterIsOnline($model, $key)
    {
        return (bool) $model->$key;
    }

    public function filterData($model, $key)
    {
        return collect(ShortDataTransformer::getAll($model->$key));
    }

    public function filterCollections($model, $key)
    {
        return collect(ShortCollectionTransformer::getAll($model->$key));
    }

    public function addHref($object, $model)
    {
        return $model->fullProfileUri($model->stn_code);
    }
}
