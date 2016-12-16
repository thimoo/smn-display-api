<?php

namespace App;

use \StdClass;
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
     * The name of the table in database
     * 
     * @var string
     */
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
     * Set the current profile online and save it
     *
     * @return void
     */
    public function setOnline()
    {
        $this->online = true;
        $this->save();
    }

    /**
     * Set the current profile offline and save it
     * 
     * @return void
     */
    public function setOffline()
    {
        $this->online = false;
        $this->last_time_online = $this->last_update;
        $this->save();
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
     * Get the most recent value between the profile and the given
     * data parameter
     * 
     * @param  Data   $data the data search
     * @return App\Value    the value serached
     */
    public function lastValue(Data $data)
    {
        return $this->values($data)->orderBy('date', 'desc')->first();
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

    /**
     * Return the boolean value of online attribute
     * 
     * @return boolean         online
     */
    public function isOnline()
    {
        return $this->online == 1;
    }

    /**
     * Return a collection of data that are display in
     * the profile as a single data
     * 
     * @return \Illuminate\Database\Eloquent\Collection   of App\Data
     */
    public function getDataDisplays()
    {
        $r = $this->data()->get();
        $collection = $r->filter(function ($data, $key) {
            return $data->pivot->data == 1;
        });
        return $collection;
    }

    /**
     * Rerturn a collection of data that are display in
     * the profile as a data collection
     * 
     * @return \Illuminate\Database\Eloquent\Collection   of App\Data
     */
    public function getCollectionDisplays()
    {
        $r = $this->data()->get();
        $collection = $r->filter(function ($data, $key) {
            return $data->pivot->collection == 1;
        });
        return $collection;
    }

    /**
     * Return the number of single data display in the profile
     * 
     * @return int number of item in collection
     */
    public function getNumberDisplays()
    {
        return $this->getDataDisplays()->count();
    }

    /**
     * Return the full URI to retreive the complete profile
     * 
     * @param  string $profile_code stn_code
     * @return string               the full URI
     */
    public function fullProfileUri($profile_code)
    {
        return route('profiles.show', ['profile' => $profile_code]);
    }

    /**
     * Return a serialized string to find the profile
     * by stn_code
     * @param  String $code the stn code to serialize
     * @return String       the serialized stn code
     */
    public static function normalizeCode(String $code)
    {
        return trim(strtolower($code));
    }

    /**
     * Set the infos attribute with a standard class
     * 
     * @param StdClass $newInfos the object to json encode
     */
    public function setNewInfos(StdClass $newInfos)
    {
        $this->infos = json_encode($newInfos);
    }
}
