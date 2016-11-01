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

    protected $table = "profiles";

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
     * Get the relation between profile and data through the
     * values table
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dataValue()
    {
        return $this->belongsToMany(
                        Data::class, 
                        'values', 
                        'profile_stn_code', 
                        'data_code'
                    )
                    ->withPivot('value', 'date', 'tag')
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

    /**
     * Create and return a new profile for the
     * given stn_code with default values
     * 
     * @param  string stn_code
     * @return App\Profile
     */
    public static function newDefault(string $stn_code)
    {
        $p = new Profile;
        $p->stn_code = $stn_code;
        $p->altitude = 0;
        $p->infos = "{}";
        $p->last_update = null;
        $p->online = true;
        $p->save();

        return $p;
    }
}
