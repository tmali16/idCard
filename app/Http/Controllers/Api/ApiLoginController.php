<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Http\Resources\UserResource;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiLoginController extends Controller
{
    use AuthenticatesUsers;
    private string $TAG = __CLASS__;
    public function __construct() {
//        $this->middleware('auth:sanctum');
    }

    public function logins(Request $request)
    {

        $attr = $request->validate([
            'username'=>'required|string',
            'password'=>'required|string',
        ]);

        $device = $request->device;

        if (!Auth::attempt($attr)) {
            return response()->json([
                'message'=>'Неправельный логин или пароль',
                'code'=>'401',
            ])->setStatusCode(401);
        }
        auth()->user()->tokens()->delete();
        return response()->json(
            [
                'token'=>auth()->user()->createToken($device ?? 'API Token')->plainTextToken
            ]
        );
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return $this->isAuth($request);
    }

    public function me(Request $request)
    {
        Log::error($this->TAG . __FUNCTION__ . " " . auth()->check());
        return new UserResource(auth()->user());
    }

    public function isAuth(Request $request)
    {
        if (auth()->check()) {
            $res = [
                'status'=>200,
                'message'=>"Вы авторизованы"
            ];
        }else{
            $res = [
                'status'=>401,
                'message'=>"Вы не авторизованы"
            ];
        }
        Log::error($this->TAG . __FUNCTION__ . " " . auth()->check());
        return response()->json($res);
    }
}
