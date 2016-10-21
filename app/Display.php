<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Display extends Model
{
    /**
     * Indicate the primary key fields
     *
     * @var array
     */
    protected $primaryKey = ['data_code', 'profile_stn_code'];

    /**
     * Indicate if the primary key is an incermetal int
     *
     * @var boll
     */
    protected $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['data', 'collection'];
}
