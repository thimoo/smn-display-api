<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class ShortDataTransformer extends Transformer
{
    use Accessors, AddFilter;

    public $attributes = [
        'code',
    ];

    public $morph = [
        'href' => '$href',
    ];

    public function addHref($object, $model)
    {
        $profile_code = $model->pivot->profile_stn_code;
        $data_code = $model->pivot->data_code;
        return $model->fullDataUri($profile_code, $data_code);
    }
}
