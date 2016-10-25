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

    protected $table = "displays";

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
    protected $fillable = ['data', 'collection'];

    /**
     * Get the profile that owns the display
     * 
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'stn_code', 'profile_stn_code');
    }

    /**
     * Get the data that owns the display
     * 
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function data()
    {
        return $this->hasOne(Data::class, 'code', 'data_code');
    }
}
