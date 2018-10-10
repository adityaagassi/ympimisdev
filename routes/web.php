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

Route::get('tes', function() {
return view('tes');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

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

Route::get('index/material', 'MaterialController@index');
Route::get('create/material', 'MaterialController@create');
Route::post('create/material', 'MaterialController@store');
Route::get('destroy/material/{id}', 'MaterialController@destroy');
Route::get('edit/material/{id}', 'MaterialController@edit');
Route::post('edit/material/{id}', 'MaterialController@update');
Route::get('show/material/{id}', 'MaterialController@show');
Route::post('import/material', 'MaterialController@import');

Route::get('index/material_volume', 'MaterialVolumeController@index');
Route::get('create/material_volume', 'MaterialVolumeController@create');
Route::post('create/material_volume', 'MaterialVolumeController@store');
Route::get('destroy/material_volume/{id}', 'MaterialVolumeController@destroy');
Route::get('edit/material_volume/{id}', 'MaterialVolumeController@edit');
Route::post('edit/material_volume/{id}', 'MaterialVolumeController@update');
Route::get('show/material_volume/{id}', 'MaterialVolumeController@show');
Route::post('import/material_volume', 'MaterialVolumeController@import');

Route::get('index/container_schedule', 'ContainerScheduleController@index');
Route::get('create/container_schedule', 'ContainerScheduleController@create');
Route::post('create/container_schedule', 'ContainerScheduleController@store');
Route::get('destroy/container_schedule/{id}', 'ContainerScheduleController@destroy');
Route::get('edit/container_schedule/{id}', 'ContainerScheduleController@edit');
Route::post('edit/container_schedule/{id}', 'ContainerScheduleController@update');
Route::get('show/container_schedule/{id}', 'ContainerScheduleController@show');
Route::post('import/container_schedule', 'ContainerScheduleController@import');

Route::get('index/production_schedule', 'ProductionScheduleController@index');
Route::get('create/production_schedule', 'ProductionScheduleController@create');
Route::post('create/production_schedule', 'ProductionScheduleController@store');
Route::get('destroy/production_schedule/{id}', 'ProductionScheduleController@destroy');
Route::get('edit/production_schedule/{id}', 'ProductionScheduleController@edit');
Route::post('edit/production_schedule/{id}', 'ProductionScheduleController@update');
Route::get('show/production_schedule/{id}', 'ProductionScheduleController@show');
Route::post('import/production_schedule', 'ProductionScheduleController@import');

Route::get('index/weekly_calendar', 'WeeklyCalendarController@index');
Route::get('create/weekly_calendar', 'WeeklyCalendarController@create');
Route::post('create/weekly_calendar', 'WeeklyCalendarController@store');
Route::get('destroy/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@destroy');
Route::get('edit/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@edit');
Route::post('edit/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@update');
Route::get('show/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@show');
Route::post('import/weekly_calendar', 'WeeklyCalendarController@import');

Route::get('index/shipment_schedule', 'ShipmentScheduleController@index');
Route::get('create/shipment_schedule', 'ShipmentScheduleController@create');
Route::post('create/shipment_schedule', 'ShipmentScheduleController@store');
Route::get('destroy/shipment_schedule/{id}', 'ShipmentScheduleController@destroy');
Route::get('edit/shipment_schedule/{id}', 'ShipmentScheduleController@edit');
Route::post('edit/shipment_schedule/{id}', 'ShipmentScheduleController@update');
Route::get('show/shipment_schedule/{id}', 'ShipmentScheduleController@show');
Route::post('import/shipment_schedule', 'ShipmentScheduleController@import');

Route::get('index/code_generator', 'CodeGeneratorController@index');
Route::get('create/code_generator', 'CodeGeneratorController@create');
Route::post('create/code_generator', 'CodeGeneratorController@store');
Route::get('destroy/code_generator/{id}', 'CodeGeneratorController@destroy');
Route::get('edit/code_generator/{id}', 'CodeGeneratorController@edit');
Route::post('edit/code_generator/{id}', 'CodeGeneratorController@update');
Route::get('show/code_generator/{id}', 'CodeGeneratorController@show');

Route::get('index/status', 'StatusController@index');
Route::get('create/status', 'StatusController@create');
Route::post('create/status', 'StatusController@store');
Route::get('destroy/status/{id}', 'StatusController@destroy');
Route::get('edit/status/{id}', 'StatusController@edit');
Route::post('edit/status/{id}', 'StatusController@update');
Route::get('show/status/{id}', 'StatusController@show');

Route::get('index/flo_view/{id}', 'FloController@index');
Route::post('index/flo_detail', 'FloController@index_flo_detail');
Route::post('index/flo', 'FloController@index_flo');
Route::post('index/flo_container', 'FloController@index_flo_container');
Route::post('scan/material_number', 'FloController@scan_material_number');
Route::post('scan/serial_number', 'FloController@scan_serial_number');
Route::post('destroy/serial_number', 'FloController@destroy_serial_number');
Route::post('scan/flo_settlement', 'FloController@flo_settlement');
Route::post('reprint/flo', 'FloController@reprint_flo');
Route::post('cancel/flo_settlement', 'FloController@cancel_flo_settlement');

Route::get('fetch/flo_container', 'FloController@fetch_flo_container');
Route::post('update/flo_container', 'FloController@update_flo_container');
Route::post('filter/flo_detail', 'FloController@filter_flo_detail');

Route::get('index/trial_export', 'TrialController@trial_index');
Route::post('export/trial', 'TrialController@trial_export');

Route::get('trial/perolehan', 'TrialController@trial_perolehan');
Route::get('trial/shipment', 'TrialController@trial_shipment');
Route::get('trial/timezone', 'TrialController@timezone');