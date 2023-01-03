<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LocationResource;
use App\Service\RequestAdapter\TerminalService;
use Illuminate\Http\Request;

class ApiRequestController extends Controller
{
    private TerminalService $terminal;

    /**
     * ApiRequestController constructor.
     */
    public function __construct(TerminalService $terminal)
    {
        $this->middleware(['auth:sanctum', 'permission:request.*']);
        $this->terminal = $terminal;
    }

    public function getMpByTerminal(Request $request){
        $request->validate([
            'phone'=>'bail|required|digits:9',
//            'device'=>'string'
        ]);
        $number = $request->get('phone');
        $mp = $this->terminal->getMp($number);
        return new LocationResource($mp);
    }
}
