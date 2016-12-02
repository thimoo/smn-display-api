<?php

namespace App\Http\Controllers;

use App\Data;
use App\Value;
use App\Profile;
use App\Collection;
use Illuminate\Http\Request;
use App\Transformers\CollectionTransformer;
use App\Transformers\SingleValueTransformer;
use App\Http\Controllers\Agregators\Agregator;

class DataController extends Controller
{
    /**
     * Show the last value in the collection between the profile and
     * the data given in parameter. If the data has a no-data tag, then
     * the last original value is attached in the original field.
     * 
     * @param  Request $request the request object
     * @param  Profile $profile the concerned profile
     * @param  Data    $data    the concerned data
     * @return Illuminate\Http\JsonResponse
     */
    public function showData(Request $request, Profile $profile, Data $data)
    {
        $value = Value::getLastValueFor($profile, $data);

        // check the aggregator
        $value->agregator = null;
        if ($request->header('agregator'))
        {
            $value->agregator = (string) $request->header('agregator');
            // Call the agregator and store new result
            // in the value
            $value = Agregator::agregate($value);
            if (!$value) return abort(422);
        }

        if ($value->isNoData())
        {
            // Get last original value
            // and attach it as orginial
            $value->original = Value::getLastOriginalValue($profile, $data);
        }

        return SingleValueTransformer::get($value);
    }

    public function test(Request $request)
    {
        $v = Value::getLastOriginalValue(Profile::find('cha'), Data::find('temp'));

        $v->tag = Value::NODATA;

        $v->original = Value::getCollectionWith('cha', 'temp')->random();

        return SingleValueTransformer::get($v);
    }

    /**
     * Show the collection between the profile and the data given in
     * parameter. A collection is composed of 144 values for the last
     * 24 hours.
     * 
     * @param  Profile $profile the concerned profile
     * @param  Data    $data    the concerned data
     * @return Illuminate\Http\JsonResponse
     */
    public function showCollection(Profile $profile, Data $data)
    {
        $collection = new Collection([
            'profile' => $profile->stn_code,
            'code' => $data->code,
            'date' => $profile->last_update,
            'values' => $profile->values($data)->orderBy('date', 'desc')->get(),
        ]);

        return CollectionTransformer::get($collection);
    }
}
