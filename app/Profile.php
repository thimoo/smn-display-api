<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * Indicate the primary key field
     *
     * @var string
     */
    protected $primaryKey = 'stn_code';

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
    protected $fillable = [
        'stn_code', 
        'altitude', 
        'infos', 
        'last_update', 
        'last_time_online', 
        'online'
    ];
}
