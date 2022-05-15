<?php


namespace App\Service;


use App\Http\Requests\GeoRequest;
use App\Http\Resources\GeoResource;
use App\Models\Geo;
use Illuminate\Http\Request;

class GeoService
{

    function getByMncLacCid(Request $request){
//        dd($request->get(''));
        $mnc = $request->get('mnc');
        $lac = $request->get('Lac');
        $ci = $request->get('Cid');

        $geo = Geo::query()
                ->where([
                    ['mnc', '=', $mnc],
                    ['lac', '=',$lac],
                    ['ci', 'like',str_replace("*", "%", $ci)],
            ])
            ->distinct(['lac', 'ci'])
            ->get();

        return GeoResource::collection($geo);
    }
}
