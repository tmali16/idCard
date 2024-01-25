<?php

namespace App\Http\Controllers;

use App\Http\Resources\GeoResource;
use App\Service\GeoService;
use Illuminate\Http\Request;
use App\Models\Fav;
class MapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(auth()->check() && auth()->user()->hasPermission('view.search_bs')){
            return response()->redirectTo('/mm');
        }
        return view('maps.map');
    }

    function pro(Request $request){
        return view('maps.pro');
    }

    public function favObjects(Request $request){
        $fav = Fav::where('state', true)->orderByDesc('created_at')->get();
        return response()->json($fav);
    }

    public function createFav(Request $request)
    {
        $all = $request->all(['full_name', 'aliase', 'phone']);

        $r = Fav::create($all);
        return response()->json([
            'msg'=>'добавлено',
            'status'=>500,
            'object'=>$r->toJson()
        ]);
    }

    public function searchBs(Request $request)
    {
        $q = $request->get('name');
        $items = [];
        if (!is_null($q)){
            preg_match("/(\d+)_(\d+)/", $q, $o);
            if(count($o) == 3){
                $items = GeoService::getByLacCid($o[1], $o[2]);
            }else{
                $items = GeoService::getBySectorName($q);
            }
        }
        return view('maps.searchbs', ['items'=>($items), 'name'=>$q]);
    }
}
