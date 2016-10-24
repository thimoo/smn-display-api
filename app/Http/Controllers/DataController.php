<?php

namespace App\Http\Controllers;

use App\Data;
use App\Profile;
use Illuminate\Http\Request;

use App\Http\Requests;

class DataController extends Controller
{
    public function showData(Profile $profile, Data $data)
    {
        return $data;
    }

    public function showCollection(Profile $profile, Data $data)
    {
        return $data;
    }
}
