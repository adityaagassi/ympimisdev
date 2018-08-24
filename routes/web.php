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


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
