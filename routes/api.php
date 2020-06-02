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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::group(['as' => 'api.', 'middleware' => ['json']], function () {
  Route::get('/movies', 'ApiMovieController@list')->name('movies.list');
  Route::post('/movies', 'ApiMovieController@add')->name('movies.add');
  Route::get('/movies/{id}', 'ApiMovieController@show')->name('movies.show');
  Route::put('/movies/{id}', 'ApiMovieController@update')->name('movies.update');
  Route::delete('/movies/{id}', 'ApiMovieController@delete')->name('movies.delete');
  
//  Route::post('/movies/{id}', 'ApiMovieController@update')->name('movies.update');
});