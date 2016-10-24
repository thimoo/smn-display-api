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
     * Indicate if the primary key is an incermetal int
     *
     * @var boll
     */
    public $incrementing = false;

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

    /**
     * Get the profile that owns the value
     * 
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'stn_code', 'profile_stn_code');
    }

    /**
     * Get the data that owns the value
     * 
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function data()
    {
        return $this->hasOne(Data::class, 'code', 'data_code');
    }
}
