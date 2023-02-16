<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fav;
class MapController extends Controller
{
    public function index()
    {
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
}
