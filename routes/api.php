<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function() {

    Route::get('profiles', 'ProfileController@index')->name('profiles.list');
    Route::get('profiles/{profile}', 'ProfileController@show')->name('profiles.show');
    Route::get('profiles/{profile}/update', 'ProfileController@checkUpdate')->name('profiles.udpate');

    Route::get('profiles/{profile}/data/{data}', 'DataController@showData')->name('profiles.data');
    Route::get('profiles/{profile}/collections/{data}', 'DataController@showCollection')->name('profiles.collections');

    Route::get('test', 'DataController@test');

});
