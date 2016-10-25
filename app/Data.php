<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    /**
     * Indicate the primary key field
     *
     * @var string
     */
    protected $primaryKey = 'code';

    protected $table = "data";

    /**
     * Indicate if the primary key is an incermetal int
     *
     * @var boll
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['code', 'smn_code'];


    /**
     * Get the profiles attached to the data 
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profiles()
    {
        return $this->belongsToMany(
                        Profile::class, 
                        'displays', 
                        'data_code', 
                        'profile_stn_code'
                    )
                    ->withPivot('data', 'collection')
                    ->withTimestamps();
    }

    /**
     * Get the displays attach between the data and the given profile
     * 
     * @param  App\Profile
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function display(Profile $profile)
    {
        return $this->hasMany(Display::class, 'data_code')
                    ->where('profile_stn_code', $profile->stn_code);
    }

    /**
     * Get the values attach between the data and the given profile
     * 
     * @param  App\Profile
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values(Profile $profile)
    {
        return $this->hasMany(Value::class, 'data_code')
                    ->where('profile_stn_code', $profile->stn_code);
    }
}
