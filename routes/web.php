<?php

use App\Value;
use App\Data;
use App\Profile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// All routes are in api group

Route::get('/', function(){
    // $p = Profile::find('jun');
    // $d = Data::first();

    // $collection = Value::getCollectionFor($p, $d);
    // $last = $collection->last();

    // // $v = $p->values($d)->where('date', '2016-10-25 11:20:00')->first();
    // // $v->tag = 'substituted';
    // // $v->save();

    // $newValue = new Value([
    //     'data_code' => 'temp',
    //     'profile_stn_code' => 'cha',
    //     'date' => '2016-10-25 11:20:00',
    //     'value' => 11.5,
    //     'tag' => 'original',
    // ]);

    // // 8.34


    // // 11.5


    // return Value::getCollectionFor($p, $d);
    // // $collection = Value::getSubstitutedLastValues($p, $d);

    // // Value::smoothSubstitutedValues($newValue, $collection);


    // // return $collection;
});