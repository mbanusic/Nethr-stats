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

Auth::routes();
Route::group(['prefix' => 'users'], function() {

	Route::get('/', 'UserController@showUsers');
	Route::get('edit/{id?}', 'UserController@editUser');
	Route::post('edit/{id?}', 'UserController@postUser');

});

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index');
Route::get('/cat/{date}', 'HomeController@cat_date');
Route::get('/{date}', 'HomeController@date');


