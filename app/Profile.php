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
    public $incrementing = false;

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

    /**
     * Get the infos json attribute decoded 
     * 
     * @param  string $value
     * @return object decoded json
     */
    public function getInfosAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Get the data attached to the profile
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function data()
    {
        return $this->belongsToMany(
                        Data::class, 
                        'displays', 
                        'profile_stn_code', 
                        'data_code'
                    )
                    ->withPivot('data', 'collection')
                    ->withTimestamps();
    }

    /**
     * Get the display relation between the profile and the given data
     * 
     * @param  Data
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function display(Data $data)
    {
        return $this->hasMany(Display::class, 'profile_stn_code')
                    ->where('data_code', $data->code);
    }

    /**
     * Get all data attached between the profile and the given data
     * 
     * @param  Data
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values(Data $data)
    {
        return $this->hasMany(Value::class, 'profile_stn_code')
                    ->where('data_code', $data->code);
    }
}
