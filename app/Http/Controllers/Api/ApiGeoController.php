<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Geo;
use App\Service\GeoService;
use Illuminate\Http\Request;

class ApiGeoController extends Controller
{
    private GeoService $geoService;

    /**
     * ApiGeoController constructor.
     * @param GeoService $geoService
     */
    public function __construct(GeoService $geoService)
    {
        $this->geoService = $geoService;
    }

    public function getByMncLacCid(Request $request){
        return $this->geoService->getByMncLacCid($request);
    }


}
