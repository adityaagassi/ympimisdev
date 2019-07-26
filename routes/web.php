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


if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::get('/tes', function () {
	return view('mails.stuffing');
});
Route::post('trial/tes', 'TrialController@tes');
Route::get('index/buffing', 'TrialController@buffingIndex');
Route::get('fetch/buffing', 'TrialController@fetchBuffingQueue');

Route::get('/tes2', function () {
	return view('purchase_orders.tes');
});

Route::get('/', function () {
	return view('auth.login');
});

Route::get('404', function() {
	return view('404');
});

Auth::routes();

Route::get('/home', ['middleware' => 'permission', 'nav' => 'Dashboard', 'uses' => 'HomeController@index'])->name('home');

Route::get('/about_mis', 'HomeController@indexAboutMIS');
Route::get('/fetch/mis_investment', 'HomeController@fetch_mis_investment');
Route::get('download/manual/{reference_file}', 'HomeController@download');

//Vistor Controll

//Vistor Controll
Route::get('visitor_index', 'VisitorController@index');
Route::get('visitor_registration', 'VisitorController@registration');
Route::get('simpan', 'VisitorController@simpanheader');
Route::get('visitor_list', 'VisitorController@receive');
Route::get('visitor_telpon', 'VisitorController@telpon');
Route::get('visitor_filllist/{nik}', 'VisitorController@filllist');
Route::get('visitor_getlist', 'VisitorController@editlist');
Route::post('visitor_inputtag', 'VisitorController@inputtag');
Route::get('visitor_confirmation', 'VisitorController@confirmation');
Route::post('visitor_updateremark', 'VisitorController@updateremark');
Route::post('visitor_updateremarkall', 'VisitorController@updateremarkall');
Route::get('visitor_leave', 'VisitorController@leave');
Route::get('visitor_getvisit', 'VisitorController@getvisit');
Route::post('visitor_out', 'VisitorController@out');
Route::get('visitor_getdata', 'VisitorController@getdata');
Route::get('visitor_display', 'VisitorController@display');
Route::get('visitor_filldisplay/{nik}', 'VisitorController@filldisplay');
Route::get('visitor_getchart', 'VisitorController@getchart');

//end visitor control 

Route::group(['nav' => 'R5', 'middleware' => 'permission'], function(){
	Route::get('index/dp_production_result', 'DisplayController@index_dp_production_result');
	Route::get('fetch/dp_production_result', 'DisplayController@fetch_dp_production_result');
	Route::get('index/wip_stock_assy', 'DisplayController@index_wip_stock_assy');

	Route::get('index/dp_stockroom_stock', 'DisplayController@index_dp_stockroom_stock');
	Route::get('fetch/dp_stockroom_stock', 'DisplayController@fetch_dp_stockroom_stock');

	Route::get('index/dp_fg_accuracy', 'DisplayController@index_dp_fg_accuracy');
	Route::get('fetch/dp_fg_accuracy', 'DisplayController@fetch_dp_fg_accuracy');
	Route::get('fetch/dp_fg_accuracy_detail', 'DisplayController@fetch_dp_fg_accuracy_detail');
});

Route::group(['nav' => 'R6', 'middleware' => 'permission'], function(){
	Route::get('index/tr_completion', 'InventoryController@indexCompletion');
	Route::get('fetch/tr_completion', 'InventoryController@fetchCompletion');
	Route::get('download/tr_completion', 'InventoryController@downloadCompletion');

	Route::get('index/tr_transfer', 'InventoryController@indexTransfer');
	Route::get('fetch/tr_transfer', 'InventoryController@fetchTransfer');
	Route::get('download/tr_transfer', 'InventoryController@downloadTransfer');

	Route::get('index/tr_history', 'InventoryController@indexHistory');
	Route::get('fetch/tr_history', 'InventoryController@fetchHistory');
});

Route::group(['nav' => 'R7', 'middleware' => 'permission'], function(){
	Route::get('index/overtime_confirmation', 'OvertimeController@indexOvertimeConfirmation');
	Route::get('fetch/overtime_confirmation', 'OvertimeController@fetchOvertimeConfirmation');
	Route::post('confirm/overtime_confirmation', 'OvertimeController@confirmOvertimeConfirmation');
	Route::post('edit/overtime_confirmation', 'OvertimeController@editOvertimeConfirmation');
	Route::post('delete/overtime_confirmation', 'OvertimeController@deleteOvertimeConfirmation');
});

Route::group(['nav' => 'R8', 'middleware' => 'permission'], function(){
	Route::get('index/report/gender', 'EmployeeController@indexReportGender');
	Route::get('fetch/report/gender', 'EmployeeController@fetchReportGender');
	Route::get('index/report/stat', 'EmployeeController@indexReportStatus');
	Route::get('index/report/grade', 'EmployeeController@indexReportGrade');
	Route::get('index/report/department', 'EmployeeController@indexReportDepartment');
	Route::get('index/report/jabatan', 'EmployeeController@indexReportJabatan');
	Route::get('fetch/report/stat', 'EmployeeController@fetchReport');
	Route::get('index/report/leave_control', 'AbsenceController@indexReportLeaveControl');
});

Route::group(['nav' => 'R9', 'middleware' => 'permission'], function(){
});

Route::get('index/report/overtime_control', 'OvertimeController@indexOvertimeControl');
Route::get('fetch/overtime_report', 'OvertimeController@overtimeReport');
Route::get('fetch/overtime_report_detail', 'OvertimeController@overtimeReportDetail');
Route::get('index/report/total_meeting', 'EmployeeController@indexTotalMeeting');
Route::get('fetch/report/gender', 'EmployeeController@fetchReportGender');
Route::get('fetch/report/status1', 'EmployeeController@fetchReportStatus');
Route::get('fetch/report/overtime_control', 'OvertimeController@OvertimeControlReport');
Route::get('fetch/report/serikat', 'EmployeeController@reportSerikat');
Route::get('fetch/report/overtime_report_control', 'OvertimeController@overtimeControl');
Route::get('fetch/overtime_report_over', 'OvertimeController@overtimeOver');
Route::get('index/employee/service', 'EmployeeController@indexEmployeeService');
Route::get('index/employee_information', 'EmployeeController@indexEmployeeInformation');

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
	Route::get('fetch/tb_monthly_summary', 'FinishedGoodsController@fetch_tb_monthly_summary');
	Route::get('index/fg_traceability', 'FinishedGoodsController@index_fg_traceability');
	Route::get('fetch/fg_traceability', 'FinishedGoodsController@fetch_fg_traceability');
	Route::get('index/fg_shipment_schedule', 'FinishedGoodsController@index_fg_shipment_schedule');
	Route::get('fetch/fg_shipment_schedule', 'FinishedGoodsController@fetch_fg_shipment_schedule');
	Route::get('index/fg_shipment_result', 'FinishedGoodsController@index_fg_shipment_result');
	Route::get('fetch/fg_shipment_result', 'FinishedGoodsController@fetch_fg_shipment_result');
	Route::get('fetch/tb_shipment_result', 'FinishedGoodsController@fetch_tb_shipment_result');
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

Route::group(['nav' => 'A7', 'middleware' => 'permission'], function(){
	Route::get('index/daily_report', 'DailyReportController@index');
	Route::post('create/daily_report', 'DailyReportController@create');
	Route::post('update/daily_report', 'DailyReportController@update');
	Route::post('delete/daily_report', 'DailyReportController@delete');
	Route::get('fetch/daily_report', 'DailyReportController@fetchDailyReport');
	Route::get('download/daily_report', 'DailyReportController@downloadDailyReport');
	Route::get('fetch/daily_report_detail', 'DailyReportController@fetchDailyReportDetail');
	Route::get('edit/daily_report', 'DailyReportController@edit');
});

Route::group(['nav' => 'A8', 'middleware' => 'permission'], function(){
	Route::get('index/middle/barrel_adjustment', 'MiddleProcessController@indexBarrelAdjustment');
	Route::get('fetch/middle/barrel_adjustment', 'MiddleProcessController@fetchBarrelAdjustment');
	Route::get('fetch/middle/barrel_inactive', 'MiddleProcessController@fetchBarrelInactive');
	Route::post('post/middle/barrel_inactive', 'MiddleProcessController@postInactive');
	Route::post('post/middle/new/barrel_inactive', 'MiddleProcessController@CreateInactive');
	Route::post('import/barrel_inactive', 'MiddleProcessController@importInactive');
});

Route::get('setting/user', 'UserController@index_setting');
Route::post('setting/user', 'UserController@setting');
	// Route::get('register', 'UserController@indexRegister');
	// Route::post('register', 'UserController@register');

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
	Route::get('delete/production_schedule', 'ProductionScheduleController@delete');
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

Route::group(['nav' => 'M16', 'middleware' => 'permission'], function(){
	Route::get('index/MasterEmp', 'EmployeeController@index');
	Route::get('fetch/masteremp', 'EmployeeController@fetchMasterEmp');	
	Route::get('fetch/masterempdetail', 'EmployeeController@fetchdetail');
	Route::post('create/empCreate', 'EmployeeController@empCreate');
	Route::post('update/empCreate', 'EmployeeController@updateEmpData');
	Route::get('index/MasterKaryawan', 'EmployeeController@index');
	Route::get('index/termination', 'EmployeeController@indexTermination');
	Route::get('index/bagian/export', 'EmployeeController@exportBagian');

	//insert
	Route::get('index/insertEmp', 'EmployeeController@insertEmp');
	Route::get('index/updateEmp/{nik}', 'EmployeeController@updateEmp');

	//import
	Route::post('import/importEmp', 'EmployeeController@importEmp');
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
	Route::get('index/maedaoshi_bi', 'MaedaoshiController@index_bi');
	Route::get('index/after_maedaoshi_bi', 'MaedaoshiController@index_after_bi');
});

Route::group(['nav' => 'S8', 'middleware' => 'permission'], function(){
	Route::get('index/maedaoshi_ei', 'MaedaoshiController@index_ei');
	Route::get('index/after_maedaoshi_ei', 'MaedaoshiController@index_after_ei');
});

Route::group(['nav' => 'S9', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/deletion', 'FloController@index_deletion');	
	Route::get('fetch/flo_deletion', 'FloController@fetch_flo_deletion');	
	Route::post('destroy/flo_deletion', 'FloController@destroy_flo_deletion');
});

Route::group(['nav' => 'S10', 'middleware' => 'permission'], function(){
	Route::post('input/process_assy_fl', 'ProcessController@inputProcessAssyFL');
	Route::post('scan/serial_number_return_fl', 'ProcessController@scanSerialNumberReturnFl');
	Route::post('stamp/adjustSerial', 'ProcessController@adjustSerial');
	Route::get('stamp/adjust', 'ProcessController@adjust');
	Route::post('stamp/adjust', 'ProcessController@adjustUpdate');
	Route::get('edit/stamp', 'ProcessController@editStamp');
	Route::post('edit/stamp', 'ProcessController@updateStamp');
	Route::post('destroy/stamp', 'ProcessController@destroyStamp');
});

Route::group(['nav' => 'S12', 'middleware' => 'permission'], function(){
	Route::get('scan/middle/kensa', 'MiddleProcessController@ScanMiddleKensa');
	Route::get('scan/middle/operator', 'MiddleProcessController@scanMiddleOperator');
	Route::post('input/middle/kensa', 'MiddleProcessController@inputMiddleKensa');
	// Route::post('input/result_middle_kensa', 'MiddleProcessController@inputResultMiddleKensa');
	Route::post('print/middle/barrel', 'MiddleProcessController@printMiddleBarrel');
	Route::post('scan/middle/barrel', 'MiddleProcessController@scanMiddleBarrel');
	Route::post('post/middle_return/barrel_return', 'MiddleProcessController@postProcessMiddleReturn');
	Route::post('post/middle_return/return_inventory', 'MiddleProcessController@postReturnInventory');
	Route::get('print/middle/barrel_reprint', 'MiddleProcessController@printMiddleBarrelReprint');
});
Route::get('fetch/result_middle_kensa', 'MiddleProcessController@fetchResultMiddleKensa');
Route::get('index/process_middle_sx', 'MiddleProcessController@indexProcessMiddleSX');
Route::get('index/process_middle_kensa/{id}', 'MiddleProcessController@indexProcessMiddleKensa');
Route::get('index/process_middle_barrel/{id}', 'MiddleProcessController@indexProcessMiddleBarrel');
Route::get('fetch/middle/barrel', 'MiddleProcessController@fetchMiddleBarrel');
Route::get('fetch/middle/barrel_machine', 'MiddleProcessController@fetchMiddleBarrelMachine');
Route::get('index/middle/barrel_machine', 'MiddleProcessController@indexProcessBarrelMachine');
Route::get('index/middle/barrel_board/{id}', 'MiddleProcessController@indexProcessBarrelBoard');
Route::get('fetch/middle/get_barrel_machine', 'MiddleProcessController@fetchProcessBarrelMachine');
Route::get('fetch/middle/get_barrel', 'MiddleProcessController@fetchProcessBarrel');
Route::get('fetch/middle/barrel_board', 'MiddleProcessController@fetchMiddleBarrelBoard');
Route::get('fetch/middle/barrel_machine_status', 'MiddleProcessController@fetchMachine');
Route::get('index/process_middle_return/{id}', 'MiddleProcessController@indexProcessMiddleReturn');
Route::get('fetch/middle_return/barrel_return', 'MiddleProcessController@fetchProcessMiddleReturn');
Route::get('fetch/middle/barrel_reprint', 'MiddleProcessController@fetchMiddleBarrelReprint');
Route::get('fetch/middle/barrel_result', 'MiddleProcessController@fetchBarrelBoardDetails');
Route::get('index/report_middle/{id}', 'MiddleProcessController@indexReportMiddle');

Route::group(['nav' => 'S13', 'middleware' => 'permission'], function(){
	Route::get('index/purchase_order/po_list', 'PurchaseOrderController@indexPoList');
	Route::get('fetch/purchase_order/po_list', 'PurchaseOrderController@fetchPoList');
	// Route::get('export/purchase_order/po_list', 'PurchaseOrderController@exportPoList');
	Route::post('import/purchase_order/po_list', 'PurchaseOrderController@importPoList');
	Route::get('index/purchase_order/po_create', 'PurchaseOrderController@indexPoCreate');
	Route::post('generate/purchase_order/po_create', 'PurchaseOrderController@generatePoCreate');
	Route::post('generate/purchase_order/po_create2', 'PurchaseOrderController@generatePoCreate2');
	Route::post('generate/purchase_order/po_create3', 'PurchaseOrderController@generatePoCreate3');
	Route::get('fetch/purchase_order/download_po', 'PurchaseOrderController@fetchDownloadPo');
	Route::get('download/purchase_order/download_po', 'PurchaseOrderController@downloadPo');
	Route::get('index/purchase_order/po_archive', 'PurchaseOrderController@indexPoArchive');
	Route::get('fetch/purchase_order/po_archive', 'PurchaseOrderController@fetchPoArchive');
	Route::get('index/purchase_order/po_revise', 'PurchaseOrderController@indexPoRevise');
	Route::post('generate/purchase_order/po_revise', 'PurchaseOrderController@generatePoRevise');
	Route::post('generate/purchase_order/po_revise2', 'PurchaseOrderController@generatePoRevise2');
	Route::post('generate/purchase_order/po_revise3', 'PurchaseOrderController@generatePoRevise2');
	Route::get('export/purchase_order/po_list', 'PurchaseOrderController@exportPoList');
	Route::get('export/purchase_order/po_list2', 'PurchaseOrderController@export')->name('export_excel.excel');
});

Route::group(['nav' => 'S14', 'middleware' => 'permission'], function(){
	Route::get('index/overtime/overtime_form', 'OvertimeController@indexOvertimeForm');
	Route::get('create/overtime/overtime_form', 'OvertimeController@createOvertimeForm');
	Route::get('select/overtime/division_hierarchy', 'OvertimeController@selectDivisionHierarchy');
	Route::get('fetch/overtime/employee', 'OvertimeController@fetchEmployee');
	Route::post('fetch/overtime/break', 'OvertimeController@fetchBreak');
	Route::post('save/overtime', 'OvertimeController@saveOvertimeHead');
	Route::post('save/overtime_detail', 'OvertimeController@saveOvertimeDetail');
	Route::post('edit/overtime_detail', 'OvertimeController@editOvertimeDetail');
	Route::get('index/overtime/print/{id}', 'OvertimeController@indexPrint');
	Route::post('fetch/report/overtime_graph', 'OvertimeController@graphPrint');
	Route::get('fetch/overtime', 'OvertimeController@fetchOvertime');
	Route::get('fetch/overtime/detail', 'OvertimeController@fetchOvertimeDetail');
	Route::post('delete/overtime', 'OvertimeController@deleteOvertime');
	Route::get('index/overtime/edit/{id}', 'OvertimeController@fetchOvertimeEdit');
});

Route::group(['nav' => 'S15', 'middleware' => 'permission'], function(){
	Route::get('index/promotion', 'EmployeeController@indexPromotion');
	Route::get('fetch/promotion', 'EmployeeController@fetchPromotion');
	Route::get('change/promotion', 'EmployeeController@changePromotion');
});

Route::group(['nav' => 'S16', 'middleware' => 'permission'], function(){
	Route::get('index/mutation', 'EmployeeController@indexMutation');
	Route::get('fetch/mutation', 'EmployeeController@fetchMutation');
	Route::get('change/mutation', 'EmployeeController@changeMutation');
});

Route::group(['nav' => 'S17', 'middleware' => 'permission'], function(){
	Route::get('index/double', 'OvertimeController@indexOvertimeDouble');
	Route::post('fetch/double', 'OvertimeController@fetchDoubleSPL');
});


//pianica


Route::group(['nav' => 'S18', 'middleware' => 'permission'], function(){
	//-----master op
	Route::get('index/Op', 'Pianica@op');
	Route::get('edit/Op', 'Pianica@editop');
	Route::post('update/Op', 'Pianica@updateop');
	Route::post('add/Op', 'Pianica@addop');


	//----------bensuki
	Route::get('index/Pianica', 'Pianica@index');	
	Route::get('index/Bensuki', 'Pianica@bensuki');
	Route::post('index/Save', 'Pianica@input');
	Route::post('index/Incoming', 'Pianica@input2');
	Route::get('index/Otokensa', 'Pianica@otokensa');
	
	//------------pureto
	Route::get('index/Pureto', 'Pianica@pureto');
	Route::get('index/op_Pureto', 'Pianica@op_pureto');
	Route::post('index/SavePureto', 'Pianica@savepureto');

	
	//------------kensa awal 
	Route::get('index/KensaAwal', 'Pianica@kensaawal');
	Route::get('index/model', 'Pianica@tag_model');
	Route::post('index/SaveKensaAwal', 'Pianica@savekensaawal');
	Route::get('index/TotalNg', 'Pianica@total_ng');


	//-----------kensa akhir
	Route::get('index/KensaAkhir', 'Pianica@kensaakhir');
	Route::post('index/SaveKensaAkhir', 'Pianica@savekensaakhir');

	//------------ kakuning visual
	Route::get('index/KakuningVisual', 'Pianica@kakuningvisual');
	Route::post('index/SaveKakuningVisual', 'Pianica@savekensaakhir');
	
	Route::get('index/FillOp', 'Pianica@fillop');
});
Route::get('index/Op_Code', 'Pianica@opcode');
	//record
Route::get('index/record', 'Pianica@recordPianica');
Route::post('index/recordPianica', 'Pianica@recordPianica2');
	//---------- report kakuning visual
Route::get('index/reportVisual', 'Pianica@reportVisual');
Route::get('index/getKensaVisualALL', 'Pianica@getKensaVisualALL');
Route::post('index/deleteInv', 'Pianica@deleteInv');
	//-------- display
Route::get('index/DisplayPN', 'Pianica@display');
Route::get('index/TotalNgAll', 'Pianica@total_ng_all');
Route::get('index/TotalNgAllLine', 'Pianica@total_ng_all_line');
Route::get('index/getTarget', 'Pianica@getTarget');
Route::get('index/GetNgBensuki', 'Pianica@GetNgBensuki');
Route::get('index/GetNgBensukiAll', 'Pianica@GetNgBensukiAll');

	//---------- report bensuki
Route::get('index/reportBensuki', 'Pianica@reportBensuki');
Route::get('index/getTotalNG', 'Pianica@getTotalNG');
Route::get('index/getMesinNg', 'Pianica@getMesinNg');

	//---------- report kensa awal
Route::get('index/reportAwal', 'Pianica@reportAwal');
Route::get('index/getKensaAwalALL', 'Pianica@getKensaAwalALL');

	//---------- report kensa awal
Route::get('index/reportAwalLine', 'Pianica@reportAwalLine');
Route::get('index/getKensaAwalALLLine', 'Pianica@getKensaAwalALLLine');

	//---------- report kensa awal
Route::get('index/reportAkhir', 'Pianica@reportAkhir');
Route::get('index/getKensaAkhirALL', 'Pianica@getKensaAkhirALL');

	//---------- report kensa awal
Route::get('index/reportAkhirLine', 'Pianica@reportAkhirLine');
Route::get('index/getKensaAkhirALLLine', 'Pianica@getKensaAkhirALLLine');

//report per tanggal
Route::get('index/reportDayAwal', 'Pianica@reportDayAwal');
Route::post('index/reportDayAwalData', 'Pianica@reportDayAwalData');
Route::get('index/reportDayAwalDataGrafik', 'Pianica@reportDayAwalDataGrafik');

Route::group(['nav' => 'S11', 'middleware' => 'permission'], function(){
	Route::get('index/CheckSheet', 'CheckSheet@index');
	Route::get('create/CheckSheet', 'CheckSheet@create');
	Route::post('import/CheckSheet', 'CheckSheet@import');
	Route::post('update/CheckSheet', 'CheckSheet@update');
	Route::get('show/CheckSheet/{id}', 'CheckSheet@show');
	Route::get('check/CheckSheet/{id}', 'CheckSheet@check');
	Route::get('checkmarking/CheckSheet/{id}', 'CheckSheet@checkmarking');
	Route::get('destroy/CheckSheet/{id}', 'CheckSheet@destroy');
	Route::post('save/CheckSheet', 'CheckSheet@save');
	Route::post('add/CheckSheet', 'CheckSheet@add');
	Route::post('addDetail/CheckSheet', 'CheckSheet@addDetail');
	Route::post('addDetail2/CheckSheet', 'CheckSheet@addDetail2');
	Route::post('nomor/CheckSheet', 'CheckSheet@nomor');
	Route::post('bara/CheckSheet', 'CheckSheet@bara');
	Route::post('edit/CheckSheet/{id}', 'CheckSheet@edit');
	Route::post('marking/CheckSheet', 'CheckSheet@marking');
	Route::post('importDetail/CheckSheet', 'CheckSheet@importDetail');
	Route::get('print/CheckSheet/{id}', 'CheckSheet@print_check');	
	Route::get('printsurat/CheckSheet/{id}', 'CheckSheet@print_check_surat');	
	Route::get('delete/CheckSheet/{id}', 'CheckSheet@delete');
	Route::get('persen/CheckSheet/{id}', 'CheckSheet@persen');
});
Route::get('stamp/stamp', 'ProcessController@stamp');
Route::post('reprint/stamp', 'ProcessController@reprint_stamp');

Route::get('index/process_assy_fl', 'ProcessController@indexProcessAssyFL');
Route::get('index/process_assy_fl_0', 'ProcessController@indexProcessAssyFL0');
Route::get('index/process_assy_fl_1', 'ProcessController@indexProcessAssyFL1');
Route::get('index/process_assy_fl_2', 'ProcessController@indexProcessAssyFL2');
Route::get('index/process_assy_fl_3', 'ProcessController@indexProcessAssyFL3');
Route::get('index/process_assy_fl_4', 'ProcessController@indexProcessAssyFL4');
Route::get('index/displayWipFl', 'ProcessController@indexDisplayWipFl');
Route::get('index/repairFl', 'ProcessController@indexRepairFl');

Route::get('fetch/wipflallstock', 'ProcessController@fetchwipflallstock');
Route::get('fetch/wipflallchart', 'ProcessController@fetchwipflallchart');
Route::get('fetch/returnTableFl', 'ProcessController@fetchReturnTableFl');
Route::get('fetch/logTableFl', 'ProcessController@fetchLogTableFl');

Route::get('fetch/process_assy_fl/actualChart', 'ProcessController@fetchProcessAssyFLActualChart');
Route::get('fetch/process_assy_fl_0/actualChart', 'ProcessController@fetchProcessAssyFL0ActualChart');
Route::get('fetch/process_assy_fl_2/actualChart', 'ProcessController@fetchProcessAssyFL2ActualChart');
Route::get('fetch/process_assy_fl_3/actualChart', 'ProcessController@fetchProcessAssyFL3ActualChart');
Route::get('fetch/process_assy_fl_4/actualChart', 'ProcessController@fetchProcessAssyFL4ActualChart');
Route::get('fetch/process_assy_fl_Display/actualChart', 'ProcessController@fetchProcessAssyFLDisplayActualChart');

Route::get('stamp/fetchPlan', 'ProcessController@fetchStampPlan');
Route::get('stamp/fetchSerialNumber', 'ProcessController@fetchSerialNumber');
Route::get('stamp/fetchResult', 'ProcessController@fetchResult');

Route::post('stamp/stamp_detail', 'ProcessController@filter_stamp_detail');
Route::get('stamp/resumes', 'ProcessController@indexResumes');
Route::get('stamp/log', 'ProcessController@indexLog');
// });



//tambah ali Saxophone & clarinet
Route::get('stamp/fetchResult/{id}', 'ProcessController@fetchResult');
Route::get('stamp/fetchPlan/{id}', 'ProcessController@fetchStampPlan');

Route::get('index/label_besar/{id}/{gmc}/{remark}', 'ProcessController@label_besar');
Route::get('index/label_kecil/{id}/{remark}', 'ProcessController@label_kecil');
Route::get('index/label_des/{id}', 'ProcessController@label_des');
Route::get('index/get_sn', 'ProcessController@getsnsax');
Route::get('index/get_sn2', 'ProcessController@getsnsax2');
Route::get('index/process_stamp_cl_1', 'ProcessController@indexProcessAssyFLCla1');
// Route::get('index/process_assy_fl_saxA_1', 'ProcessController@indexProcessAssyFLSaxA1');
Route::get('index/process_stamp_sx_1', 'ProcessController@indexProcessAssyFLSaxT1');
Route::get('index/process_stamp_sx_2', 'ProcessController@indexProcessAssyFLSaxT2');
Route::get('index/process_stamp_sx_3', 'ProcessController@indexProcessAssyFLSaxT3');
Route::post('index/print_sax', 'ProcessController@print_sax');
Route::post('index/print_sax2', 'ProcessController@print_sax2');
Route::get('stamp/fetchStampPlansax2/{id}', 'ProcessController@fetchStampPlansax2');
Route::get('stamp/fetchStampPlansax3/{id}', 'ProcessController@fetchStampPlansax3');
Route::post('reprint/stamp2', 'ProcessController@reprint_stamp2');
Route::get('index/getModel', 'ProcessController@getModel');
Route::get('edit/stampLabel', 'ProcessController@editStampLabel');
Route::post('edit/stampLabel', 'ProcessController@updateStampLabel');
Route::get('index/reprintLabel', 'ProcessController@getModelReprint');
Route::get('index/getdatareprintAll', 'ProcessController@getModelReprintAll');

Route::get('index/process_stamp_cl', 'ProcessController@indexProcessStampCl');
Route::get('index/process_stamp_sx', 'ProcessController@indexProcessStampSX');
Route::get('stamp/resumes_cl', 'ProcessController@indexResumesCl');
Route::post('stamp/stamp_detail_cl', 'ProcessController@filter_stamp_detail_cl');
Route::get('stamp/resumes_sx', 'ProcessController@indexResumesSX');
Route::post('stamp/stamp_detail_sx', 'ProcessController@filter_stamp_detail_sx');
Route::get('fetch/fetch_plan_labelsax/{id}', 'ProcessController@fetch_plan_labelsax');

//end tambah ali

Route::get('scan/maedaoshi_material', 'MaedaoshiController@scan_maedaoshi_material');
Route::get('scan/maedaoshi_serial', 'MaedaoshiController@scan_maedaoshi_serial');
Route::get('fetch/maedaoshi', 'MaedaoshiController@fetch_maedaoshi');
Route::get('reprint/maedaoshi', 'MaedaoshiController@reprint_maedaoshi');
Route::post('destroy/maedaoshi', 'MaedaoshiController@destroy_maedaoshi');

Route::get('scan/after_maedaoshi_material', 'MaedaoshiController@scan_after_maedaoshi_material');
Route::get('scan/after_maedaoshi_serial', 'MaedaoshiController@scan_after_maedaoshi_serial');

Route::group(['nav' => 'R1', 'middleware' => 'permission'], function(){
	Route::get('index/flo_view/detail', 'FloController@index_detail');
});

Route::post('index/flo_detail', 'FloController@index_flo_detail');
Route::post('index/flo_invoice', 'FloController@index_flo_invoice');
Route::post('index/flo', 'FloController@index_flo');
Route::get('index/flo_container', 'FloController@index_flo_container');
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


//DISPLAY EXPORT PROGRESS
Route::get('fetch/display/shipment_progress', 'DisplayController@fetchShipmentProgress');
Route::get('fetch/display/modal_shipment_progress', 'DisplayController@fetchModalShipmentProgress');
Route::get('index/display/shipment_progress', 'DisplayController@indexShipmentProgress');
