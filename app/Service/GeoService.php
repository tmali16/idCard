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
                ['cid', '=', $ci],
            ])->first();
    }

    public static function getBySectorName(string $sector){
        return  Geo::query()->whereRaw('lower(sector_name) = lower(?)', [$sector])->distinct(['lac', 'cid'])->get();
    }

    public static function getByLacCid($lac, $ci){
        return Geo::query()
            ->where([
                ['lac', '=', $lac],
                ['cid', '=', $ci],
            ])->distinct(['lac', 'cid'])->get();
    }

    public static function getBs($mnc, $lac, $ci){
        return  Geo::query()
            ->where([
                ['mnc', '=', $mnc],
                ['lac', '=', $lac],
                ['cid', '=', $ci],
            ])->first();
    }

    public function fullSearch(Request $request){
        $txt = $request->get('text');
        if(is_numeric(str_replace(" ","",$txt))){
            $mnc = $request->get('mnc');
            preg_match('/(\d+)\s+(\d+)/', $txt, $o);
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
                    ['cid', 'like', str_replace("*", "%", $ci) . "%"],
                ]);
//            $this->historyService->create($mnc, $lac, $ci, $geoQuery?->first()?->id);
        }else{
//            if(is_string($txt)){
            $geoQuery = Geo::query()->where([
                    ['sector_name', "ilike", (str_replace("*", "%", $txt) . "%")]
                ])->distinct(['sector_name', 'g']);
//            }
        }
        return GeoResource::collection($geoQuery->distinct(['lac', 'ci', 'g'])->get());
    }

    function getByMncLacCid(Request $request){
//        dd($request->get(''));
        $bs_name = $request->get('bs_name');
        if($bs_name){
            if(strlen($bs_name) > 3) {
                $geoQuery = Geo::query()
                    ->where([
                        ['sector_name', "ilike", mb_strtoupper($bs_name)]
                    ])->distinct(['sector_name', 'diapason']);
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
                    ['cid', 'like', str_replace("*", "%", $ci) . "%"],
                ]);
//            $this->historyService->create($mnc, $lac, $ci, $geoQuery?->first()?->id);
        }
        return GeoResource::collection($geoQuery->distinct(['lac', 'cid', 'diapason'])->get());
    }
}
