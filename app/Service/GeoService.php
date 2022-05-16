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
        $lacCi = $request->get('Lac');
//        $ci = $request->get('Cid');
        $LacCi = explode(":", trim($lacCi));
//        dd(count($LacCi));
        if (count($LacCi) <= 1 || !$mnc){
            return response()->json([
                'status'=>500,
                'messages'=>'Данные введены не верно!'
            ]);
        }
        $lac = $LacCi[0];
        $ci = $LacCi[1];
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
