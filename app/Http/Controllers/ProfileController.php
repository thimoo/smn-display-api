<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProfileController extends Controller
{
    public function index()
    {
        return Profile::all();
    }

    public function show(Profile $profile)
    {
        return $profile;
    }

    public function checkUpdate(Profile $profile, $date)
    {
        // transforme the date to a carbon date
        // check if the profile have been updated after the date
        // return the response
    }
}
