<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    /**
     * Indicate the primary key fields
     *
     * @var array
     */
    protected $primaryKey = ['data_code', 'profile_stn_code', 'date'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'date', 
    	'value', 
    	'tag'
    ];
}
