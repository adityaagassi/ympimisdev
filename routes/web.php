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
    return view('auth.login');
});

Route::get('404', function() {
return view('404');
});

Route::get('signin', function() {
return view('signin');
});

Route::get('tes', function() {
return view('tes');
});
// Route::get('create', function() {
// return view('users.create');
// });
Route::get('index/user', 'UserController@index');
Route::get('create/user', 'UserController@create');
Route::post('create/user','UserController@store');
Route::get('destroy/user/{id}', 'UserController@destroy');
Route::get('edit/user/{id}', 'UserController@edit');
Route::post('edit/user/{id}', 'UserController@update');
Route::get('show/user/{id}', 'UserController@show');

Route::get('index/level', 'LevelController@index');
Route::get('create/level', 'LevelController@create');
Route::post('create/level','LevelController@store');
Route::get('destroy/level/{id}', 'LevelController@destroy');
Route::get('edit/level/{id}', 'LevelController@edit');
Route::post('edit/level/{id}', 'LevelController@update');
Route::get('show/level/{id}', 'LevelController@show');

Route::get('index/container', 'ContainerController@index');
Route::get('create/container', 'ContainerController@create');
Route::post('create/container', 'ContainerController@store');
Route::get('destroy/container/{id}', 'ContainerController@destroy');
Route::get('edit/container/{id}', 'ContainerController@edit');
Route::post('edit/container/{id}', 'ContainerController@update');
Route::get('show/container/{id}', 'ContainerController@show');

Route::get('index/destination', 'DestinationController@index');
Route::get('create/destination', 'DestinationController@create');
Route::post('create/destination', 'DestinationController@store');
Route::get('destroy/destination/{id}', 'DestinationController@destroy');
Route::get('edit/destination/{id}', 'DestinationController@edit');
Route::post('edit/destination/{id}', 'DestinationController@update');
Route::get('show/destination/{id}', 'DestinationController@show');

Route::get('index/shipment_condition', 'ShipmentConditionController@index');
Route::get('create/shipment_condition', 'ShipmentConditionController@create');
Route::post('create/shipment_condition', 'ShipmentConditionController@store');
Route::get('destroy/shipment_condition/{id}', 'ShipmentConditionController@destroy');
Route::get('edit/shipment_condition/{id}', 'ShipmentConditionController@edit');
Route::post('edit/shipment_condition/{id}', 'ShipmentConditionController@update');
Route::get('show/shipment_condition/{id}', 'ShipmentConditionController@show');

Route::get('index/origin_group', 'OriginGroupController@index');
Route::get('create/origin_group', 'OriginGroupController@create');
Route::post('create/origin_group', 'OriginGroupController@store');
Route::get('destroy/origin_group/{id}', 'OriginGroupController@destroy');
Route::get('edit/origin_group/{id}', 'OriginGroupController@edit');
Route::post('edit/origin_group/{id}', 'OriginGroupController@update');
Route::get('show/origin_group/{id}', 'OriginGroupController@show');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
