<?php

namespace App\Transformers;

use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class ShortCollectionTransformer extends ShortDataTransformer
{
    use Accessors, AddFilter;

    /**
     * Add a new attribute to the result object.
     * Create a link to refresh the collection
     *
     * @param mixed $object the result object
     * @param mixed $model  the source model
     * @return string
     */
    public function addHref($object, $model)
    {
        $profile_code = $model->pivot->profile_stn_code;
        $data_code = $model->pivot->data_code;
        return $model->fullDataUri($profile_code, $data_code, 'collections');
    }
}
