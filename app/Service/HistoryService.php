<?php


namespace App\Service;


use App\Http\Resources\HistoryResource;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryService
{

    public function save($mnc, $lac, $ci, ?int $cell_id){
        return History::create([
            'user_id'=>Auth::id(),
            'mnc'=>$mnc,
            'lac'=>$lac,
            'ci'=>$ci,
            'cell_id'=> $cell_id,
        ]);
    }

    public function getLastRequest(?Request $request=null){
        abort_if(!$request?->ajax(), 401);
        $history = History::query()
            ->where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->subHours(24)->format('Y-m-d h:i:s'),now()->format('Y-m-d h:i:s')])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
        return HistoryResource::collection($history);
    }

    public static function create($mnc, $lac, $ci, ?int $cell_id){
        return (new HistoryService())->save($mnc, $lac, $ci, $cell_id);
    }

    public static function getLast(){
        return (new HistoryService())->getLastRequest();
    }

}
