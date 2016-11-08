<?php

namespace App\Http\Controllers;

use StdClass;
use App\Profile;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\Transformer;
use App\Transformers\ProfileTransformer;

class ProfileController extends Controller
{
    /**
     * Stores the date used to compare if an update is
     * available
     * 
     * @var Carbon\Carbon
     */
    private $date;

    /**
     * Return a list of all profiles available in the service
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return ProfileTransformer::all(Profile::all());
    }

    /**
     * Show the profile with a list of all data and collections available
     * 
     * @param  Profile $profile the profile
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Profile $profile)
    {
        $profile->data_display = $profile->getDataDisplays();
        $profile->collections_display = $profile->getCollectionDisplays();

        return ProfileTransformer::get($profile);
    }

    /**
     * Check if new values in data or collection are available for
     * the given profile since the given date in headers
     * 
     * @param  Request $request the request
     * @param  Profile $profile the profile
     * @return Illuminate\Http\JsonResponse
     */
    public function checkUpdate(Request $request, Profile $profile)
    {
        // transforme the date to a carbon date
        // check if the profile have been updated after the date
        // return the response
        if ($request->header('X-Datetime'))
        {
            $this->date = new Carbon($request->header('X-Datetime'));
            $transformer = new Transformer;
            $pDate = new Carbon($profile->last_update);
            $response = new StdClass;
            $response->compareDate = $request->header('X-Datetime');
            $response->lastUpdate = (new Carbon($profile->last_update))->toW3cString();
            $response->updateAvailable = $this->date->lt($pDate);
            return $transformer->wrap($response);
        }
        else return abort(422);
    }
}
