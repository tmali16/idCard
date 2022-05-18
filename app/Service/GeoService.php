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


    function getByMncLacCid(Request $request){
//        dd($request->get(''));
        $mnc = $request->get('mnc');
        $lacCi = $request->get('LacCid');
        preg_match('/(\d+)\s+(\d+)/', $lacCi, $o);
//        dd($o);
        if (count($o) !== 3 || !$mnc){
            return response()->json([
                'status'=>500,
                'messages'=>'Данные введены не верно!'
            ]);
        }
        $lac = $o[1];
        $ci = $o[2];
        $geo = Geo::query()
                ->where([
                    ['mnc', '=', $mnc],
                    ['lac', '=',$lac],
                    ['ci', 'like', str_replace("*", "%", $ci)."%"],
            ])
            ->distinct(['lac', 'ci', 'diapason'])
            ->get();

        $this->historyService->create($mnc, $lac, $ci, $geo?->first()?->id);

        return GeoResource::collection($geo);
    }
}
