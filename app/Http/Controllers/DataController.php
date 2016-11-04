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

    public function showCollection(Profile $profile, Data $data)
    {
        $collection = new Collection([
            'code' => $data->code,
            'date' => $profile->last_update,
            'values' => $profile->values($data)->orderBy('date', 'desc')->get(),
        ]);
        // var_dump($profile->values($data)->orderBy('date', 'desc')->get());
        return CollectionTransformer::get($collection);
    }
}
