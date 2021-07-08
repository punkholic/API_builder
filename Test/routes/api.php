<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/testView','App\Http\Controllers\TestTestController@moreView');

Route::post('/testView2','App\Http\Controllers\TestTestController@testView1');

Route::get('/testView5','App\Http\Controllers\TestTestController@testView2');

Route::post('/testView3','App\Http\Controllers\TestTestController@testView3');

Route::post('/testView4','App\Http\Controllers\TestTestController@testView4');

Route::get('/moreView','App\Http\Controllers\moreMoreController@moreView1');

Route::post('/moreView2','App\Http\Controllers\moreMoreController@moreView2');

Route::get('/moreView5','App\Http\Controllers\moreMoreController@moreView3');

Route::post('/moreView3','App\Http\Controllers\moreMoreController@moreView4');

Route::post('/moreView4','App\Http\Controllers\moreMoreController@moreView5');
