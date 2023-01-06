<?php


namespace App\Service;


use App\Http\Requests\GeoRequest;
use App\Http\Resources\GeoResource;
use App\Models\Geo;
use Illuminate\Http\Request;

class GeoService
{
    private HistoryService $historyService;

    /**
     * GeoService constructor.
     * @param HistoryService $historyService
     */
    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    public static function get($mnc, $lac, $ci){
        return  Geo::query()
            ->where([
                ['mnc', '=', $mnc],
                ['lac', '=', $lac],
                ['ci', '=', $ci],
            ])->first();
    }

    public static function getBs($lac, $ci){
        return  Geo::query()
            ->where([
                ['lac', '=', $lac],
                ['ci', '=', $ci],
            ])->first();
    }

    function getByMncLacCid(Request $request){
//        dd($request->get(''));
        $bs_name = $request->get('bs_name');
        if($bs_name){
            if(strlen($bs_name) > 3) {
                $geoQuery = Geo::query()
                    ->where([
                        ['sectorname', "ilike", mb_strtoupper($bs_name)]
                    ])->distinct(['sectorname', 'diapason']);
            }else{
                return response()->json([
                    'status' => 500,
                    'messages' => 'Напишите полное название сектора!'
                ]);
            }
        }else {
            $mnc = $request->get('mnc');
            $lacCi = $request->get('LacCid');
            preg_match('/(\d+)\s+(\d+)/', $lacCi, $o);
            if (count($o) !== 3 || !$mnc) {
                return response()->json([
                    'status' => 500,
                    'messages' => 'Данные введены не верно!'
                ]);
            }
            $lac = $o[1];
            $ci = $o[2];
            $geoQuery = Geo::query()
                ->where([
                    ['mnc', '=', $mnc],
                    ['lac', '=', $lac],
                    ['ci', 'like', str_replace("*", "%", $ci) . "%"],
                ]);
            $this->historyService->create($mnc, $lac, $ci, $geoQuery?->first()?->id);
        }
        return GeoResource::collection($geoQuery->distinct(['lac', 'ci', 'diapason'])->get());
    }
}
