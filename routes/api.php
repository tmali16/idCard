<?php


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', [\App\Http\Controllers\Api\ApiLoginController::class, 'logins']);

Route::prefix('/profile')->middleware('auth:sanctum')->group(function () {
    Route::get("/auth",   [\App\Http\Controllers\Api\ApiLoginController::class, 'isAuth']);
    Route::get('/me',     [\App\Http\Controllers\Api\ApiLoginController::class, 'me']);
    Route::get('/logout', [\App\Http\Controllers\Api\ApiLoginController::class, 'logout']);
});

Route::prefix('/request')->middleware(['auth:sanctum', 'permission:request.*'])->group(function(){
    Route::get('/bs', [\App\Http\Controllers\Api\ApiRequestController::class, 'getMpByTerminal']);//->middleware('permission:request.terminal');
    Route::get('/bs/info', [\App\Http\Controllers\Api\ApiRequestController::class, 'getBsByLaCi']);//->middleware('permission:request.*');
});

