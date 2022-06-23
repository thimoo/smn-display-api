<?php

namespace App\Transformers;

use \StdClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Transformer
{
    /**
     * Stored the data response
     *
     * @var StdClass
     */
    protected $response;

    /**
     * Stores the attributes array used by the filter
     * method
     *
     * @var array
     */
    public $attributes = [];

    /**
     * Store the key value array to morph model attribute
     * keys
     *
     * @var array
     */
    public $morph = [];

    /**
     * Construct a new transformer object
     */
    public function __construct()
    {
        $this->response = new StdClass;
    }

    /**
     * Transform the given model in a JsonResponse
     *
     * @param  Model                        $model the eloquent model to transform
     * @return Illuminate\Http\JsonResponse        the response
     */
    public function transform(Model $model)
    {
        return $this->filter($model);
    }

    /**
     * Transform a collection of model in a JsonResponse
     *
     * @param  Collection                    $models the eloquent collection
     * @return Illuminate\Http\JsonResponse          the response
     */
    public function transformAll(Collection $models)
    {
        $models->transform(function ($model, $key) {
           return $this->transform($model);
        });

        return array_values($models->toArray());
    }

    /**
     * Wrap the result in a laravel JsonResponse
     *
     * @param  mixed        $output the content to wrape
     * @return JsonResponse         the response container
     */
    public function wrap($output)
    {
        return $this->setData($output)->respond($output);
    }

    /**
     * Parse the input model and check if attributes must
     * be display in the response object
     *
     * @param  Illuminate\Database\Eloquent\Model $model model to filter
     * @return StdClass                                  response class
     */
    protected function filter($model)
    {
        $o = new StdClass;
        foreach($model->getAttributes() as $key => $value)
        {
            if (in_array($key, $this->attributes))
            {
                $newkey = $this->morph($key);
                $o->$newkey = $this->filterContent($model, $key);
            }
        }
        return $o;
    }

    /**
     * Check if a filter function is available for the current
     * key. If yes, then return the result of the called function
     * with model and key. Else return the result directly from the
     * model
     *
     * @param  Illuminate\Database\Eloquent\Model $model the model
     * @param  string                             $key   key property
     * @return mixed                                     the content
     */
    protected function filterContent($model, $key)
    {
        $callable = $this->getFuncName($key);
        if (method_exists($this, $callable))
        {
            return $this->$callable($model, $key);
        }
        else return $model->$key;
    }

    /**
     * Get the filter function name based on the morphed key property
     *
     * @param  string $key the property name ex: last_update
     * @return string      ex: filterLastUpdate
     */
    protected function getFuncName($key)
    {
        return 'filter' . ucfirst($this->morph($key));
    }

    /**
     * Return the new key if present in the morph array, else
     * return the same key
     *
     * @param  string $key key to morph
     * @return string      the new key value
     */
    protected function morph($key)
    {
        if (array_key_exists($key, $this->morph))
        {
            return $this->morph[$key];
        }
        else return $key;
    }

    /**
     * Set the data attribute in the response object
     *
     * @param  StdClass $data any filtered model
     * @return Importer $this
     */
    protected function setData($data)
    {
        $this->response->data = $data;

        return $this;
    }

    /**
     * Set an error message for the response
     *
     * @param string  $message "An error occured, please retry later!"
     * @param integer $code    422
     */
    protected function setError($message = "An error occured, please retry later!", $code = 422)
    {
        $this->response->error = $code;
        $this->response->data->message = $message;
    }

    /**
     * Return a new JsonResponse with the data setup
     * into the Standard class store in response attribute
     *
     * @return Illuminate\Http\JsonResponse      a Laravel Json response
     */
    protected function respond()
    {
        $response = new JsonResponse;
        return $response->setData($this->response);
    }
}
