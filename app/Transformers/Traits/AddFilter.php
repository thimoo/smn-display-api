<?php

namespace App\Transformers\Traits;

use \ReflectionMethod;

trait AddFilter
{
    /**
     * Extend the behavior to call the add magic functions
     * 
     * @param  Model $model an eloquent model
     * @return mixed        a data object
     */
    protected function filter($model)
    {
        $o = parent::filter($model);
        $this->newFields($o, $model);
        return $o;
    }

    /**
     * Browe all class methods and check if the method is
     * a magic add method to call
     * 
     * @param  mixed $object the result object
     * @param  Model $model  an eloquent model
     * @return void
     */
    protected function newFields($object, $model)
    {
        foreach (get_class_methods(self::class) as $method)
        {
            if (preg_match('/add(.*)/', $method) > 0)
            {
                $this->newField($object, $model, $method);
            }
        }
    }

    /**
     * Call the magic add method and store the result in
     * the result object
     * 
     * @param  object $object the result object
     * @param  Model  $model  an eloquent model
     * @param  string $method the magic add method name
     * @return void
     */
    protected function newField($object, $model, $method)
    {
        $res = (new ReflectionMethod(self::class, $method))
            ->invoke($this, $object, $model);

        $fieldKey = $this->fieldForAdd($method);
        $object->$fieldKey = $res;
    }

    /**
     * Return the name of the new field added. Morph the
     * result name to implement the morph process
     * 
     * @param  string $method the magic add method name
     * @return string         the final field name
     */
    protected function fieldForAdd($method)
    {
        $name = preg_replace("/add(.*)/", "$1", $method);
        return $this->morph(lcfirst($name));
    }
}
