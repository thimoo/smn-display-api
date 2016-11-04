<?php

namespace App\Transformers\Traits;

trait Linkable
{
    /**
     * Return the base API URI
     * 
     * @return string full uri
     */
    public function baseUri()
    {
        return env('APP_URL') . "/" . config('constants.api_version') . "/" ;
    }
}
