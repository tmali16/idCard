<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Geo;
use App\Service\GeoService;
use App\Service\HistoryService;
use Illuminate\Http\Request;

class ApiGeoController extends Controller
{
    private GeoService $geoService;
    protected HistoryService $historyService;

    /**
     * ApiGeoController constructor.
     * @param GeoService $geoService
     */
    public function __construct(GeoService $geoService, HistoryService $historyService)
    {
        $this->middleware(['auth']);
        $this->geoService = $geoService;
        $this->historyService = $historyService;
    }

    public function getByMncLacCid(Request $request){
        return $this->geoService->getByMncLacCid($request);
    }

    function getLastRequest(Request $request){
        return $this->historyService->getLastRequest($request);
    }


}
