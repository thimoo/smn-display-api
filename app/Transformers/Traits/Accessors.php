<?php

namespace App\Transformers\Traits;

trait Accessors
{
    /**
     * Return the content transformed into a JsonResponse
     *
     * @param  Model                        $input the model to transform
     * @return Illuminate\Http\JsonResponse        the response
     */
    public static function get($input)
    {
        return (new self)->wrap(self::getSingle($input));
    }

    /**
     * Return the collection transformed into a JsonResponse
     *
     * @param  Collection              $collection the collection to transform
     * @return Illuminate\Http\JsonResponse        the response
     */
    public static function all($collection)
    {
        return (new self)->wrap(self::getAll($collection));
    }

    /**
     * Return the transformed content
     *
     * @param  Model   $input eloquent model
     * @return object         the response
     */
    public static function getSingle($input)
    {
        return (new self)->transform($input);
    }

    /**
     * Return the transformed collection
     *
     * @param  Collection $collection eloquent collection
     * @return array                  the response
     */
    public static function getAll($collection)
    {
        return (new self)->transformAll($collection);
    }
}
