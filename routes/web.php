<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['login']);

Route::prefix('control')->controller()->group(function(){

});

Route::controller(\App\Http\Controllers\MapController::class)->middleware(['auth'])->group(function (){
    Route::get("/", 'index');
});

Route::get('/te', function (){
    return bcrypt(70043);
});

Route::prefix('api')->controller(\App\Http\Controllers\Api\ApiGeoController::class)->group(function (){
    Route::post('geo/search', 'getByMncLacCid');
    Route::get('geo/history', 'getLastRequest');
});
