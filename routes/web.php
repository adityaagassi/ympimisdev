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

Auth::routes();

Route::get('/home', ['middleware' => 'permission', 'nav' => 'Dashboard', 'uses' => 'HomeController@index'])->name('home');

Route::group(['nav' => 'R5', 'middleware' => 'permission'], function(){
	Route::get('index/dp_production_result', 'DisplayController@index_dp_production_result');
	Route::get('fetch/dp_production_result', 'DisplayController@fetch_dp_production_result');
});

Route::group(['nav' => 'R3', 'middleware' => 'permission'], function(){
	Route::get('index/fg_production', 'FinishedGoodsController@index_fg_production');
	Route::get('fetch/fg_production', 'FinishedGoodsController@fetch_fg_production');
	Route::get('fetch/tb_production', 'FinishedGoodsController@fetch_tb_production');
	Route::get('index/fg_stock', 'FinishedGoodsController@index_fg_stock');
	Route::get('fetch/fg_stock', 'FinishedGoodsController@fetch_fg_stock');
	Route::get('fetch/tb_stock', 'FinishedGoodsController@fetch_tb_stock');
	Route::get('index/fg_container_departure', 'FinishedGoodsController@index_fg_container_departure');
	Route::get('fetch/fg_container_departure', 'FinishedGoodsController@fetch_fg_container_departure');
	Route::get('fetch/tb_container_departure', 'FinishedGoodsController@fetch_tb_container_departure');
	Route::get('download/att_container_departure', 'FinishedGoodsController@download_att_container_departure');
	Route::get('index/fg_weekly_summary', 'FinishedGoodsController@index_fg_weekly_summary');
	Route::get('fetch/fg_weekly_summary', 'FinishedGoodsController@fetch_fg_weekly_summary');
	Route::get('index/fg_monthly_summary', 'FinishedGoodsController@index_fg_monthly_summary');
	Route::get('fetch/fg_monthly_summary', 'FinishedGoodsController@fetch_fg_monthly_summary');
	Route::get('index/fg_traceability', 'FinishedGoodsController@index_fg_traceability');
	Route::get('fetch/fg_traceability', 'FinishedGoodsController@fetch_fg_traceability');
	Route::get('index/fg_shipment_schedule', 'FinishedGoodsController@index_fg_shipment_schedule');
	Route::get('fetch/fg_shipment_schedule', 'FinishedGoodsController@fetch_fg_shipment_schedule');
});

Route::group(['nav' => 'R4', 'middleware' => 'permission'], function(){
	Route::get('index/ch_daily_production_result', 'ChoreiController@index_ch_daily_production_result');
	Route::get('fetch/daily_production_result_week', 'ChoreiController@fetch_daily_production_result_week');
	Route::get('fetch/daily_production_result_date', 'ChoreiController@fetch_daily_production_result_date');
	Route::get('fetch/daily_production_result', 'ChoreiController@fetch_daily_production_result');
	Route::get('fetch/production_result_modal', 'ChoreiController@fetch_production_result_modal');
	Route::get('fetch/production_accuracy_modal', 'ChoreiController@fetch_production_accuracy_modal');
	Route::get('fetch/production_bl_modal', 'ChoreiController@fetch_production_bl_modal');
});

Route::group(['nav' => 'R2', 'middleware' => 'permission'], function(){
	Route::get('index/inventory', 'InventoryController@index');
	Route::post('fetch/inventory', 'InventoryController@fetch');
});

Route::group(['nav' => 'A1', 'middleware' => 'permission'], function(){
	Route::get('index/batch_setting', 'BatchSettingController@index');
	Route::get('create/batch_setting', 'BatchSettingController@create');
	Route::post('create/batch_setting','BatchSettingController@store');
	Route::get('destroy/batch_setting/{id}', 'BatchSettingController@destroy');
	Route::get('edit/batch_setting/{id}', 'BatchSettingController@edit');
	Route::post('edit/batch_setting/{id}', 'BatchSettingController@update');
	Route::get('show/batch_setting/{id}', 'BatchSettingController@show');
});

Route::group(['nav' => 'A6', 'middleware' => 'permission'], function(){
	Route::get('index/user', 'UserController@index');
	Route::get('create/user', 'UserController@create');
	Route::post('create/user','UserController@store');
	Route::get('destroy/user/{id}', 'UserController@destroy');
	Route::get('edit/user/{id}', 'UserController@edit');
	Route::post('edit/user/{id}', 'UserController@update');
	Route::get('show/user/{id}', 'UserController@show');
});

Route::group(['nav' => 'A3', 'middleware' => 'permission'], function(){
	Route::get('index/navigation', 'NavigationController@index');
	Route::get('create/navigation', 'NavigationController@create');
	Route::post('create/navigation','NavigationController@store');
	Route::get('destroy/navigation/{id}', 'NavigationController@destroy');
	Route::get('edit/navigation/{id}', 'NavigationController@edit');
	Route::post('edit/navigation/{id}', 'NavigationController@update');
	Route::get('show/navigation/{id}', 'NavigationController@show');
});

Route::group(['nav' => 'A4', 'middleware' => 'permission'], function(){
	Route::get('index/role', 'RoleController@index');
	Route::get('create/role', 'RoleController@create');
	Route::post('create/role','RoleController@store');
	Route::get('destroy/role/{id}', 'RoleController@destroy');
	Route::get('edit/role/{id}', 'RoleController@edit');
	Route::post('edit/role/{id}', 'RoleController@update');
	Route::get('show/role/{id}', 'RoleController@show');
});

Route::group(['nav' => 'M1', 'middleware' => 'permission'], function(){
	Route::get('index/container', 'ContainerController@index');
	Route::get('create/container', 'ContainerController@create');
	Route::post('create/container', 'ContainerController@store');
	Route::get('destroy/container/{id}', 'ContainerController@destroy');
	Route::get('edit/container/{id}', 'ContainerController@edit');
	Route::post('edit/container/{id}', 'ContainerController@update');
	Route::get('show/container/{id}', 'ContainerController@show');
});

Route::group(['nav' => 'M3', 'middleware' => 'permission'], function(){
	Route::get('index/destination', 'DestinationController@index');
	Route::get('create/destination', 'DestinationController@create');
	Route::post('create/destination', 'DestinationController@store');
	Route::get('destroy/destination/{id}', 'DestinationController@destroy');
	Route::get('edit/destination/{id}', 'DestinationController@edit');
	Route::post('edit/destination/{id}', 'DestinationController@update');
	Route::get('show/destination/{id}', 'DestinationController@show');
	Route::post('import/destination', 'DestinationController@import');
});

Route::group(['nav' => 'M8', 'middleware' => 'permission'], function(){
	Route::get('index/shipment_condition', 'ShipmentConditionController@index');
	Route::get('create/shipment_condition', 'ShipmentConditionController@create');
	Route::post('create/shipment_condition', 'ShipmentConditionController@store');
	Route::get('destroy/shipment_condition/{id}', 'ShipmentConditionController@destroy');
	Route::get('edit/shipment_condition/{id}', 'ShipmentConditionController@edit');
	Route::post('edit/shipment_condition/{id}', 'ShipmentConditionController@update');
	Route::get('show/shipment_condition/{id}', 'ShipmentConditionController@show');
});

Route::group(['nav' => 'M6', 'middleware' => 'permission'], function(){
	Route::get('index/origin_group', 'OriginGroupController@index');
	Route::get('create/origin_group', 'OriginGroupController@create');
	Route::post('create/origin_group', 'OriginGroupController@store');
	Route::get('destroy/origin_group/{id}', 'OriginGroupController@destroy');
	Route::get('edit/origin_group/{id}', 'OriginGroupController@edit');
	Route::post('edit/origin_group/{id}', 'OriginGroupController@update');
	Route::get('show/origin_group/{id}', 'OriginGroupController@show');
});

Route::group(['nav' => 'M4', 'middleware' => 'permission'], function(){
	Route::get('index/material', 'MaterialController@index');
	Route::get('create/material', 'MaterialController@create');
	Route::post('create/material', 'MaterialController@store');
	Route::get('destroy/material/{id}', 'MaterialController@destroy');
	Route::get('edit/material/{id}', 'MaterialController@edit');
	Route::post('edit/material/{id}', 'MaterialController@update');
	Route::get('show/material/{id}', 'MaterialController@show');
	Route::post('import/material', 'MaterialController@import');
});

Route::group(['nav' => 'M5', 'middleware' => 'permission'], function(){
	Route::get('index/material_volume', 'MaterialVolumeController@index');
	Route::get('create/material_volume', 'MaterialVolumeController@create');
	Route::post('create/material_volume', 'MaterialVolumeController@store');
	Route::get('destroy/material_volume/{id}', 'MaterialVolumeController@destroy');
	Route::get('edit/material_volume/{id}', 'MaterialVolumeController@edit');
	Route::post('edit/material_volume/{id}', 'MaterialVolumeController@update');
	Route::get('show/material_volume/{id}', 'MaterialVolumeController@show');
	Route::post('import/material_volume', 'MaterialVolumeController@import');
});

Route::group(['nav' => 'M2', 'middleware' => 'permission'], function(){
	Route::get('index/container_schedule', 'ContainerScheduleController@index');
	Route::get('create/container_schedule', 'ContainerScheduleController@create');
	Route::post('create/container_schedule', 'ContainerScheduleController@store');
	Route::get('destroy/container_schedule/{id}', 'ContainerScheduleController@destroy');
	Route::get('edit/container_schedule/{id}', 'ContainerScheduleController@edit');
	Route::post('edit/container_schedule/{id}', 'ContainerScheduleController@update');
	Route::get('show/container_schedule/{id}', 'ContainerScheduleController@show');
	Route::post('import/container_schedule', 'ContainerScheduleController@import');
});

Route::group(['nav' => 'M7', 'middleware' => 'permission'], function(){
	Route::get('index/production_schedule', 'ProductionScheduleController@index');
	Route::get('create/production_schedule', 'ProductionScheduleController@create');
	Route::post('create/production_schedule', 'ProductionScheduleController@store');
	Route::get('destroy/production_schedule/{id}', 'ProductionScheduleController@destroy');
	Route::get('edit/production_schedule/{id}', 'ProductionScheduleController@edit');
	Route::post('edit/production_schedule/{id}', 'ProductionScheduleController@update');
	Route::get('show/production_schedule/{id}', 'ProductionScheduleController@show');
	Route::post('import/production_schedule', 'ProductionScheduleController@import');
});

Route::group(['nav' => 'M10', 'middleware' => 'permission'], function(){
	Route::get('index/weekly_calendar', 'WeeklyCalendarController@index');
	Route::get('create/weekly_calendar', 'WeeklyCalendarController@create');
	Route::post('create/weekly_calendar', 'WeeklyCalendarController@store');
	Route::get('destroy/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@destroy');
	Route::get('edit/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@edit');
	Route::post('edit/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@update');
	Route::get('show/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@show');
	Route::post('import/weekly_calendar', 'WeeklyCalendarController@import');
});

Route::group(['nav' => 'M9', 'middleware' => 'permission'], function(){
	Route::get('index/shipment_schedule', 'ShipmentScheduleController@index');
	Route::get('create/shipment_schedule', 'ShipmentScheduleController@create');
	Route::post('create/shipment_schedule', 'ShipmentScheduleController@store');
	Route::get('destroy/shipment_schedule/{id}', 'ShipmentScheduleController@destroy');
	Route::get('edit/shipment_schedule/{id}', 'ShipmentScheduleController@edit');
	Route::post('edit/shipment_schedule/{id}', 'ShipmentScheduleController@update');
	Route::get('show/shipment_schedule/{id}', 'ShipmentScheduleController@show');
	Route::post('import/shipment_schedule', 'ShipmentScheduleController@import');
});

Route::group(['nav' => 'A2', 'middleware' => 'permission'], function(){
	Route::get('index/code_generator', 'CodeGeneratorController@index');
	Route::get('create/code_generator', 'CodeGeneratorController@create');
	Route::post('create/code_generator', 'CodeGeneratorController@store');
	Route::get('destroy/code_generator/{id}', 'CodeGeneratorController@destroy');
	Route::get('edit/code_generator/{id}', 'CodeGeneratorController@edit');
	Route::post('edit/code_generator/{id}', 'CodeGeneratorController@update');
	Route::get('show/code_generator/{id}', 'CodeGeneratorController@show');
});

Route::group(['nav' => 'A5', 'middleware' => 'permission'], function(){
	Route::get('index/status', 'StatusController@index');
	Route::get('create/status', 'StatusController@create');
	Route::post('create/status', 'StatusController@store');
	Route::get('destroy/status/{id}', 'StatusController@destroy');
	Route::get('edit/status/{id}', 'StatusController@edit');
	Route::post('edit/status/{id}', 'StatusController@update');
	Route::get('show/status/{id}', 'StatusController@show');
});

Route::group(['nav' => 'S1', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/bi', 'FloController@index_bi');
});

Route::group(['nav' => 'S2', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/ei', 'FloController@index_ei');
});

Route::group(['nav' => 'S3', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/delivery', 'FloController@index_delivery');
});

Route::group(['nav' => 'S4', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/stuffing', 'FloController@index_stuffing');
});

Route::group(['nav' => 'S5', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/shipment', 'FloController@index_shipment');
});

Route::group(['nav' => 'S6', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/lading', 'FloController@index_lading');
});

Route::group(['nav' => 'S7', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/maedaoshi', 'MaedaoshiController@index');
});

Route::group(['nav' => 'R1', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/detail', 'FloController@index_detail');
});

Route::post('index/flo_detail', 'FloController@index_flo_detail');
Route::post('index/flo_invoice', 'FloController@index_flo_invoice');
Route::post('index/flo', 'FloController@index_flo');
Route::post('index/flo_container', 'FloController@index_flo_container');
Route::post('scan/material_number', 'FloController@scan_material_number');
Route::post('scan/serial_number', 'FloController@scan_serial_number');
Route::post('destroy/serial_number', 'FloController@destroy_serial_number');
Route::post('destroy/flo_attachment', 'FloController@destroy_flo_attachment');
Route::post('scan/flo_settlement', 'FloController@flo_settlement');
Route::post('reprint/flo', 'FloController@reprint_flo');
Route::post('cancel/flo_settlement', 'FloController@cancel_flo_settlement');
Route::get('fetch/flo_container', 'FloController@fetch_flo_container');
Route::get('fetch/flo_lading', 'FloController@fetch_flo_lading');
Route::post('input/flo_lading', 'FloController@input_flo_lading');
Route::post('update/flo_container', 'FloController@update_flo_container');
Route::post('filter/flo_detail', 'FloController@filter_flo_detail');