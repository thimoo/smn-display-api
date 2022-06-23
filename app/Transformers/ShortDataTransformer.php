<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class ShortDataTransformer extends Transformer
{
    use Accessors, AddFilter;

    /**
     * List of json attributes
     *
     * @var array
     */
    public $attributes = [
        'code',
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
     * Add a new attribute to the result object.
     * Create a link to refresh the data
     *
     * @param mixed $object the result object
     * @param mixed $model  the source model
     * @return string
     */
    public function addHref($object, $model)
    {
        $profile_code = $model->pivot->profile_stn_code;
        $data_code = $model->pivot->data_code;
        return $model->fullDataUri($profile_code, $data_code);
    }
}
