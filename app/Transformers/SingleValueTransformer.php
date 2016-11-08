<?php

namespace App\Transformers;

use App\Data;
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
        'href' => '$href',
    ];

    public function filterValue($model, $key)
    {
        return (float) $model->$key;
    }

    public function filterOriginal($model, $key)
    {
        if ($model->$key)
        {
            $original = SingleValueTransformer::getSingle($model->$key);
            unset($original->{'$href'});
            return $original;
        }
    }

    public function addHref($object, $model)
    {
        $profile_code = $model->profile_stn_code;
        $data_code = $model->data_code;
        return Data::fullUri($profile_code, $data_code);
    }
}
