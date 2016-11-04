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
    private $date;

    public function index()
    {
        return ProfileTransformer::all(Profile::all());
    }

    public function show(Profile $profile)
    {
        $profile->data_display = $profile->getDataDisplays();
        // var_dump($profile->data_display);
        $profile->collections_display = $profile->getCollectionDisplays();

        return ProfileTransformer::get($profile);
    }

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
            $response->lastUpdate = $profile->last_update;
            $response->updateAvailable = $this->date->lt($pDate);
            return $transformer->wrap($response);
        }
        else return abort(422);
    }
}
