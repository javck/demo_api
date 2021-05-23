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

Route::get('/hello/{name}', 'App\Http\Controllers\Api\HelloController@hello');
Route::post('/hw/times', 'App\Http\Controllers\Api\HwController@times');

Route::prefix('posts')->group(function () {
    //無印版
    Route::post('/', 'App\Http\Controllers\Api\PostController@store');
    Route::get('/{post}', 'App\Http\Controllers\Api\PostController@show');
    Route::get('/', 'App\Http\Controllers\Api\PostController@index');
    Route::put('/post_tag', 'App\Http\Controllers\Api\PostController@updateTag');
    Route::put('/{post}', 'App\Http\Controllers\Api\PostController@update');
    Route::delete('/{post}', 'App\Http\Controllers\Api\PostController@destroy');
});
