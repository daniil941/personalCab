<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('send_interkassa', 'InterkasaController@sendRequest');

Route::post('success', 'InterkasaController@success');
Route::post('fail', 'InterkasaController@fail');
Route::post('pending', 'InterkasaController@pending');
Route::post('interaction', 'InterkasaController@interaction');