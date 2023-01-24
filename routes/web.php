<?php

use Illuminate\Support\Facades\Auth;
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

//Route::prefix('control')->controller()->group(function(){
//
//});

Route::controller(\App\Http\Controllers\MapController::class)->middleware(['auth'])->group(function (){
    Route::get("/", 'index');
});

Route::get('/te', function (){
    return bcrypt('PolkI');
});

Route::get('/l', [\App\Http\Controllers\PulseController::class, 'page'])->middleware(['auth', 'permission:request.terminal']);
Route::get('/ls', [\App\Http\Controllers\PulseController::class, 'sendPulse'])->middleware(['auth', 'permission:request.terminal']);

Route::prefix('/user')->name('user.')->middleware(['auth', 'permission:user.*'])->group(function(){
    Route::get('/', [\App\Http\Controllers\Auth\UserController::class, 'index'])->name('index')->middleware('permission:user.view');
    Route::get('/rud/{id}', [\App\Http\Controllers\Auth\UserController::class, 'rud'])->middleware('permission:user.view');
    Route::get('/create', [\App\Http\Controllers\Auth\UserController::class, 'create'])->middleware('permission:user.*')->name('create');
    Route::get('/store', [\App\Http\Controllers\Auth\UserController::class, 'save'])->middleware('permission:user.*');
});

Route::prefix('api')->controller(\App\Http\Controllers\Api\ApiGeoController::class)->group(function (){
    Route::post('geo/search', 'getByMncLacCid');
    Route::get('geo/history', 'getLastRequest');
});
