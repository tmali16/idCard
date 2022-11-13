<?php


namespace App\Http\Controllers;



use App\Http\Resources\TerminalLocationResource;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PulseController extends Controller
{
    private $client;
    protected $url;

    public function __construct()
    {
        $this->middleware('auth');
        if(auth()->check() && auth()->user()->username !== 'admin'){
            abort(404, '_NOT_FOUND');
        }else{
            $this->url = env('TERMINAL_API_URL');
            $this->client = new Client([
                'base_uri' => $this->url,
                'timeout' => 60.0,
            ]);
        }
    }

    function page(Request $request){
        return response()->view('maps.location');
    }

    function sendPulse(Request $request){
        return $this->get($request->get('num'));
    }

    function get($phone_number)
    {
        $uri = "/api/info?phone=996".$phone_number;
        $content = $this->client->get($uri);
        $str = $content->getBody()->getContents();
        $erp = json_decode($str, true);
        $erp['data'] = new TerminalLocationResource($erp['data']);
        return response()->json($erp);
    }
}
