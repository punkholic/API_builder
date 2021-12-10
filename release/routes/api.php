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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/testView','App\Http\Controllers\BlogController@moreView');

Route::post('/testView2/{id}/{value}','App\Http\Controllers\BlogController@testView1');

Route::get('/testView5','App\Http\Controllers\BlogController@testView2');

Route::post('/testView3/{id}/{value}','App\Http\Controllers\BlogController@testView3');

Route::post('/testView4','App\Http\Controllers\BlogController@testView4');

Route::get('/getMore','App\Http\Controllers\MoreController@moreView1');

Route::post('/storeMore','App\Http\Controllers\MoreController@moreView2');

Route::post('/moreView5','App\Http\Controllers\MoreController@store');

Route::post('/editMore/{id}','App\Http\Controllers\MoreController@moreView4');

Route::delete('/deleteMore/{id}','App\Http\Controllers\MoreController@moreView5');
