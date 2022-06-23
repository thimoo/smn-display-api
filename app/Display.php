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
     * The name of the table in database
     *
     * @var string
     */
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

    /**
     * Sync the relation between the given data and
     * the given profile to set the data attribute to
     * true
     *
     * @param  Data    $data    the data to sync with
     * @param  Profile $profile the profile to sync with
     * @return void
     */
    public static function showData(Data $data, Profile $profile)
    {
        self::checkRelation($data, $profile);

        $profile->data()->updateExistingPivot($data->code, ['data' => true]);

    }

    /**
     * Sync the relation between the given data and
     * the given profile to set the collection attribute
     * to true
     *
     * @param  Data    $data    the data to sync with
     * @param  Profile $profile the profile to sync with
     * @return void
     */
    public static function showCollection(Data $data, Profile $profile)
    {
        self::checkRelation($data, $profile);

        $profile->data()->updateExistingPivot($data->code, ['collection' => true]);
    }

    /**
     * Sync the relation between the given data and
     * the given profile to set the data attrivute to
     * false
     *
     * @param  Data    $data    the data to sync with
     * @param  Profile $profile the profile to sync with
     * @return void
     */
    public static function hideData(Data $data, Profile $profile)
    {
        self::checkRelation($data, $profile);

        $profile->data()->updateExistingPivot($data->code, ['data' => false]);
    }

    /**
     * Sync the relation between the given data and
     * the given profile to set the collection attribute
     * to false
     *
     * @param  Data    $data    the data to sync with
     * @param  Profile $profile the profile to sync with
     * @return void
     */
    public static function hideCollection(Data $data, Profile $profile)
    {
        self::checkRelation($data, $profile);

        $profile->data()->updateExistingPivot($data->code, ['collection' => false]);

    }

    /**
     * Check if the relation between the given data and
     * the given profile already exist. If not, then the
     * relation is created with default data and collection
     * value to false
     *
     * @param  Data    $data    the data to sync with
     * @param  Profile $profile the profile to sync with
     * @return void
     */
    protected static function checkRelation(Data $data, Profile $profile)
    {
        // Count the number of relation between the profile
        // and the data with the right code
        $count = $profile
            ->data()
            ->where('data_code', $data->code)
            ->count();

        // If no relation is find, create a new one with
        // default value to false
        if ($count == 0)
        {
            $profile->data()->save($data, [
                'data' => false,
                'collection' => false
            ]);
        }
    }
}
