<?php

namespace App\Transformers;

use App\Data;
use Carbon\Carbon;
use App\Transformers\Traits\Accessors;
use App\Transformers\Traits\AddFilter;

class SingleValueTransformer extends Transformer
{
    use Accessors, AddFilter;

    /**
     * List of json attributes
     * 
     * @var array
     */
    public $attributes = [
        'data_code',
        'date',
        'value',
        'tag',
        'agregator',
        'original',
    ];

    /**
     * List of renamed attributes
     * 
     * @var array
     */
    public $morph = [
        'data_code' => 'code',
        'href' => '$href',
    ];

    /**
     * Filter the value attribute. Cast in float
     * 
     * @param  mixed  $model the result object
     * @param  string $key   the attribute name
     * @return float
     */
    public function filterValue($model, $key)
    {
        return (float) $model->$key;
    }

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
     * Filter the original value. If a original value is
     * present in the model, transform and delete the href
     * attribute
     * 
     * @param  mixed  $model the source model
     * @param  string $key   the attribute name
     * @return mixed
     */
    public function filterOriginal($model, $key)
    {
        if ($model->$key)
        {
            $original = SingleValueTransformer::getSingle($model->$key);
            unset($original->{'$href'});
            return $original;
        }
        return null;
    }

    /**
     * Add a new attribute to the result object.
     * Create a link to refresh the single value
     * 
     * @param mixed $object the result object
     * @param mixed $model  the source model
     * @return string
     */
    public function addHref($object, $model)
    {
        $profile_code = $model->profile_stn_code;
        $data_code = $model->data_code;
        return Data::fullUri($profile_code, $data_code);
    }
}
