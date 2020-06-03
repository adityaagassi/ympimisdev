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

Route::get('trial2', 'TrialController@trial2');


Route::get('fetch_trial2', 'StockTakingController@printSummary');
Route::get('trial_print', 'StockTakingController@printSummary');

Route::get('trial_loc', 'TrialController@trialLoc');
Route::get('trial_loc2/{lat}/{long}', 'TrialController@getLocation');


Route::get('/index/emergency_response', 'TrialController@tes2');
Route::get('/index/trials', 'TrialController@tes');
Route::get('/index/unification_uniform', 'VoteController@indexUnificationUniform');
Route::get('fetch/employee/data', 'TrialController@fetch_data');
Route::get('happybirthday', 'TrialController@ultah');


Route::get('trialmail', 'TrialController@trialmail');

Route::get('/trial', function () {
	return view('meetings.report');
});
Route::get('/trial3', function () {
	return view('trial');
});

Route::get('/trialPrint', function () {
	return view('maintenance/apar/print');
});
Route::get('/index/apar/print', function () {
	return view('maintenance/apar/aparPrint');
});

Route::get('/qr', function () {
	return view('maintenance/apar/aparQr');
});

Route::get('/fetch/trial2', 'PlcController@fetchTemperature');
Route::get('print/trial', 'TrialController@stocktaking');
Route::get('trial_machine', 'TrialController@fetch_machine');

Route::get('/machinery_monitoring', function () {
	return view('plant_maintenance.machinery_monitoring', array(
		'title' => 'Machinery Monitoring',
		'title_jp' => ''
	));
});



Route::get('/information_board', function () {
	return view('information_board')->with('title', 'INFORMATION BOARD')->with('title_jp', '情報板');
});

Auth::routes();

Route::get('/', function () {
	if (Auth::check()) {
		if (Auth::user()->role_code == 'emp-srv') {
			// return redirect()->action('EmployeeController@indexEmployeeService', ['id' => 1]);
			return redirect()->route('emp_service', ['id' => 1]);
			// return redirect()->route('index/employee/service/{ctg}', ['ctg' => 'home']);
		} else {
			return view('home');
		}
	} else {
		return view('auth.login');
	}
});

Route::get('404', function() {
	return view('404');
});

Route::get('/home', ['middleware' => 'permission', 'nav' => 'Dashboard', 'uses' => 'HomeController@index'])->name('home');

Route::get('/about_mis', 'HomeController@indexAboutMIS');
Route::get('/project_timeline', 'HomeController@indexProjectTimeline');
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
Route::get('visitor_confirm_manager/{id}', 'VisitorNotificationController@confirm_manager');
Route::get('visitor_leave', 'VisitorController@leave');
Route::get('visitor_getvisit', 'VisitorController@getvisit');
Route::post('visitor_out', 'VisitorController@out');
Route::get('visitor_getdata', 'VisitorController@getdata');
Route::get('visitor_display', 'VisitorController@display');
Route::get('visitor_filldisplay/{nik}', 'VisitorController@filldisplay');
Route::get('visitor_getchart', 'VisitorController@getchart');

Route::get('visitor_getvisitSc', 'VisitorController@confirmation2');

Route::get('visitor_confirmation_manager', 'VisitorController@confirmation_manager');
Route::get('fetch/visitor/fetchVisitorByManager', 'VisitorController@fetchVisitorByManager');
Route::get('scan/visitor/lobby', 'VisitorController@scanVisitorLobby');

//end visitor control 

//VISITOR TEMPERATURE

Route::get('index/temperature', 'TemperatureController@index');
Route::get('index/temperature/body_temperature_report', 'TemperatureController@indexBodyTemperatureReport');
Route::get('fetch/temperature/body_temp_report', 'TemperatureController@fetchBodyTemperatureReport');
Route::get('index/temperature/body_temp_monitoring', 'TemperatureController@indexBodyTempMonitoring');
Route::get('fetch/temperature/fetch_body_temp_monitoring', 'TemperatureController@fetchBodyTempMonitoring');

//END VISITOR TEMPERATURE
Route::get('index/temperature/omron/{id}', 'TemperatureController@indexOmron');
Route::get('fetch/temperature/omron', 'TemperatureController@fetchOmron');
Route::post('input/temperature/omron_operator', 'TemperatureController@inputOmronOperator');

//OMRON TEMPERATURE

// ROOM Temperature
Route::get('index/temperature/room_temperature', 'TemperatureController@RoomTemperature');
Route::get('fetch/temperature/room_temperature', 'TemperatureController@fetchRoomTemperature');

//----- Start mesin injeksi
Route::get('scan/injeksi/operator', 'InjectionsController@scanInjectionOperator');
Route::get('index/injeksi', 'InjectionsController@index');
Route::post('index/injeksi/store_ng', 'InjectionsController@store_ng');
Route::post('index/injeksi/store_ng_temp', 'InjectionsController@store_ng_temp');
Route::post('index/injeksi/update_ng_temp', 'InjectionsController@update_ng_temp');
Route::post('index/injeksi/store_molding_log', 'InjectionsController@store_molding_log');
Route::get('index/injeksi/get_ng_temp', 'InjectionsController@get_ng_temp');
Route::get('index/injeksi/get_molding_log', 'InjectionsController@get_molding_log');
Route::post('index/injeksi/delete_ng_temp', 'InjectionsController@delete_ng_temp');
Route::get('index/machine_operational', 'InjectionsController@indexMachineSchedule');
//in
Route::get('index/in', 'InjectionsController@in');
Route::post('scan/part_injeksi', 'InjectionsController@scanPartInjeksi');
Route::post('scan/part_molding', 'InjectionsController@scanPartMolding');
Route::get('send/Part', 'InjectionsController@sendPart');
Route::get('get/Inpart', 'InjectionsController@getDataIn');
//end in

// out
Route::get('index/out', 'InjectionsController@out');
Route::get('get/Outpart', 'InjectionsController@getDataOut');
//end out

// ---- dailyStock
Route::get('index/dailyStock', 'InjectionsController@dailyStock');
Route::get('fetch/dailyStock', 'InjectionsController@getDailyStock');
// ---- end dailyStock
Route::get('fetch/InOutpart', 'InjectionsController@getDataInOut');

//schedule
Route::get('index/Schedule', 'InjectionsController@schedule');
Route::get('fetch/Schedulepart', 'InjectionsController@getSchedule');
Route::get('fetch/getStatusMesin', 'InjectionsController@getStatusMesin');
Route::get('fetch/schedule', 'InjectionsController@fetchSchedule');

Route::get('fetch/getDateWorking', 'InjectionsController@getDateWorking');
Route::post('save/Schedule', 'InjectionsController@saveSchedule');

Route::post('save/Scheduletmp', 'InjectionsController@saveScheduleTmp');

Route::get('fetch/getChartPlan', 'InjectionsController@getChartPlan');

Route::get('fetch/percenMesin', 'InjectionsController@percenMesin');

Route::get('fetch/mjblue', 'InjectionsController@detailPartMJBlue');
Route::get('fetch/headblue', 'InjectionsController@detailPartHeadBlue');
Route::get('fetch/footblue', 'InjectionsController@detailPartFootBlue');
Route::get('fetch/blockblue', 'InjectionsController@detailPartBlockBlue');
Route::get('fetch/injeksiVsAssyBlue', 'InjectionsController@injeksiVsAssyBlue');

Route::get('fetch/injeksiVsAssyGreen', 'InjectionsController@injeksiVsAssyGreen');
Route::get('fetch/injeksiVsAssyPink', 'InjectionsController@injeksiVsAssyPink');
Route::get('fetch/injeksiVsAssyRed', 'InjectionsController@injeksiVsAssyRed');
Route::get('fetch/injeksiVsAssyBrown', 'InjectionsController@injeksiVsAssyBrown');
Route::get('fetch/injeksiVsAssyIvory', 'InjectionsController@injeksiVsAssyIvory');
Route::get('fetch/injeksiVsAssyYrf', 'InjectionsController@injeksiVsAssyYrf');

Route::get('fetch/chartWorkingMachine', 'InjectionsController@chartWorkingMachine');

//end schedule

//report stock

Route::get('index/reportStock', 'InjectionsController@reportStock');
Route::get('fetch/getDataStock', 'InjectionsController@getDataStock');

Route::get('index/MonhtlyStock', 'InjectionsController@indexMonhtlyStock');
Route::get('fetch/MonhtlyStock', 'InjectionsController@MonhtlyStock');

Route::get('fetch/MonhtlyStockAllYrf', 'InjectionsController@MonhtlyStockAllYrf');
Route::get('fetch/MonhtlyStockAll', 'InjectionsController@MonhtlyStockAll');
Route::get('fetch/MonhtlyStockHead', 'InjectionsController@MonhtlyStockHead');
Route::get('fetch/MonhtlyStockFoot', 'InjectionsController@MonhtlyStockFoot');
Route::get('fetch/MonhtlyStockBlock', 'InjectionsController@MonhtlyStockBlock');

Route::get('index/dailyNG', 'InjectionsController@indexDailyNG');
Route::get('fetch/dailyNG', 'InjectionsController@dailyNG');
Route::get('fetch/detailDailyNG', 'InjectionsController@detailDailyNG');

Route::get('index/molding_monitoring', 'InjectionsController@index_molding_monitoring');
Route::get('fetch/molding_monitoring', 'InjectionsController@molding_monitoring');
Route::get('fetch/detail_molding_monitoring', 'InjectionsController@detail_molding_monitoring');

//end report


// mesin
Route::get('index/mesin', 'InjectionsController@mesin');
Route::get('fetch/getDataMenit', 'InjectionsController@getDataMenit');
Route::get('fetch/getDataMesinShoot', 'InjectionsController@getDataMesinShoot');


// end mesin


// operator
Route::get('index/opmesin', 'InjectionsController@opmesin');
Route::get('input/statusmesin', 'InjectionsController@inputStatusMesin');
Route::post('delete/statusmesin', 'InjectionsController@deleteStatusMesin');
Route::get('get/statusmesin', 'InjectionsController@getStatusMesin');

Route::post('input/logmesin', 'InjectionsController@logmesin');
Route::get('get/getDataMesinShootLog', 'InjectionsController@getDataMesinShootLog');
Route::get('get/getDataMesinStatusLog', 'InjectionsController@getDataMesinStatusLog');

// end operator

//report stock

Route::get('index/reportStockMonitoring', 'InjectionsController@reportStockMonitoring');
Route::get('fetch/getTargetWeek', 'InjectionsController@getTargetWeek');
//end report

//report balance mesin

Route::get('index/reportBalanceMesin', 'InjectionsController@reportBalanceMesin');
Route::get('fetch/getBalanceMesin', 'InjectionsController@getBalanceMesin');

Route::post('input/makePlan', 'InjectionsController@makePlan');
Route::get('fetch/getBalanceMesinChart', 'InjectionsController@getBalanceMesinChart');
//end balance mesin



//master machine
Route::get('index/masterMachine', 'InjectionsController@masterMachine');
Route::get('fetch/fillMasterMachine', 'InjectionsController@fillMasterMachine');
Route::get('fetch/editMasterMachine', 'InjectionsController@editMasterMachine');
Route::post('fetch/updateMasterMachine', 'InjectionsController@updateMasterMachine');
Route::post('fetch/addMasterMachine', 'InjectionsController@addMasterMachine');

Route::get('fetch/chartMasterMachine', 'InjectionsController@chartMasterMachine');

Route::get('index/masterCycleMachine', 'InjectionsController@masterCycleMachine');
Route::get('fetch/fillMasterCycleMachine', 'InjectionsController@fillMasterCycleMachine');
Route::get('fetch/chartMasterCycleMachine', 'InjectionsController@chartMasterCycleMachine');


Route::get('get/workingPartMesin', 'InjectionsController@workingPartMesin');


//end master machine


// ------------- start 3 hari

Route::get('index/indexPlanAll', 'InjectionsController@indexPlanAll');
Route::get('fetch/getPlanAll', 'InjectionsController@getPlanAll');

// ------------- end start 3 hari


//molding injection

Route::get('index/injection/molding', 'InjectionsController@molding');
Route::get('get/injeksi/get_molding', 'InjectionsController@get_molding');
Route::get('get/injeksi/get_molding_pasang', 'InjectionsController@get_molding_pasang');
Route::get('fetch/injeksi/fetch_molding', 'InjectionsController@fetch_molding');
Route::get('fetch/injeksi/fetch_molding_pasang', 'InjectionsController@fetch_molding_pasang');
Route::post('index/injeksi/store_history_temp', 'InjectionsController@store_history_temp');
Route::get('index/injeksi/get_history_temp', 'InjectionsController@get_history_temp');
Route::post('index/injeksi/update_history_temp', 'InjectionsController@update_history_temp');
Route::post('index/injeksi/store_history_molding', 'InjectionsController@store_history_molding');

//end molding injection

//maintenance molding injection

Route::get('index/injection/molding_maintenance', 'InjectionsController@molding_maintenance');
Route::get('get/injeksi/get_molding_master', 'InjectionsController@get_molding_master');
Route::get('fetch/injeksi/fetch_molding_master', 'InjectionsController@fetch_molding_master');
Route::post('index/injeksi/store_maintenance_temp', 'InjectionsController@store_maintenance_temp');
Route::get('index/injeksi/get_maintenance_temp', 'InjectionsController@get_maintenance_temp');
Route::post('index/injeksi/update_maintenance_temp', 'InjectionsController@update_maintenance_temp');
Route::post('index/injeksi/store_maintenance_molding', 'InjectionsController@store_maintenance_molding');


//end maintenance molding injection

// end mesin injeksi

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

//REPAIR FLUTE
Route::get('flute_repair', 'AdditionalController@indexFluteRepair');
Route::get('index/flute_repair/tarik', 'AdditionalController@indexTarik');
Route::get('fetch/flute_repair/tarik', 'AdditionalController@fetchTarik');
Route::post('scan/flute_repair/tarik', 'AdditionalController@scanTarik');
Route::get('index/flute_repair/selesai', 'AdditionalController@indexSelesai');
Route::get('fetch/flute_repair/selesai', 'AdditionalController@fetchSelesai');
Route::post('scan/flute_repair/selesai', 'AdditionalController@scanSelesai');
Route::get('index/flute_repair/kembali', 'AdditionalController@indexKembali');
Route::get('fetch/flute_repair/kembali', 'AdditionalController@fetchKembali');
Route::post('scan/flute_repair/kembali', 'AdditionalController@scanKembali');
Route::get('index/flute_repair/resume', 'AdditionalController@indexResume');
Route::get('fetch/flute_repair/by_status', 'AdditionalController@fetchByStatus');
Route::get('fetch/flute_repair/by_model', 'AdditionalController@fetchByModel');
Route::get('fetch/flute_repair/by_date', 'AdditionalController@fetchByDate');

//REPAIR RECORDER
Route::get('recorder_repair', 'AdditionalController@indexRecorderRepair');
Route::get('index/recorder_repair/tarik', 'AdditionalController@indexRecorderTarik');
Route::get('fetch/recorder_repair/tarik', 'AdditionalController@fetchRecorderTarik');
Route::post('scan/recorder_repair/tarik', 'AdditionalController@scanRecorderTarik');

Route::get('index/recorder_repair/selesai', 'AdditionalController@indexRecorderSelesai');
Route::get('fetch/recorder_repair/selesai', 'AdditionalController@fetchRecorderSelesai');
Route::post('scan/recorder_repair/selesai', 'AdditionalController@scanRecorderSelesai');


Route::get('index/recorder_repair/kembali', 'AdditionalController@indexRecorderKembali');
Route::get('fetch/recorder_repair/kembali', 'AdditionalController@fetchRecorderKembali');
Route::post('scan/recorder_repair/kembali', 'AdditionalController@scanRecorderKembali');

Route::get('index/recorder_repair/resume', 'AdditionalController@indexRecorderResume');
Route::get('fetch/recorder_repair/by_status', 'AdditionalController@fetchRecorderByStatus');
Route::get('fetch/recorder_repair/by_model', 'AdditionalController@fetchRecorderByModel');
Route::get('fetch/recorder_repair/by_date', 'AdditionalController@fetchRecorderByDate');

//EMPLOYEE
Route::group(['nav' => 'R10', 'middleware' => 'permission'], function(){
	Route::get('index/report/manpower', 'EmployeeController@indexReportManpower');
	Route::get('fetch/report/manpower', 'EmployeeController@fetchReportManpower');
	Route::get('fetch/report/manpower_detail', 'EmployeeController@fetchReportManpowerDetail');
});


Route::get('index/report/employee_resume', 'EmployeeController@indexEmployeeResume');
Route::get('fetch/report/employee_resume', 'EmployeeController@fetchEmployeeResume');

Route::get('index/report/gender', 'EmployeeController@indexReportGender');
Route::get('fetch/report/gender2', 'EmployeeController@fetchReportGender2');
Route::get('index/report/stat', 'EmployeeController@indexReportStatus');
Route::get('index/report/grade', 'EmployeeController@indexReportGrade');
Route::get('index/report/department', 'EmployeeController@indexReportDepartment');
Route::get('index/report/jabatan', 'EmployeeController@indexReportJabatan');
Route::get('fetch/report/stat', 'EmployeeController@fetchReport');
Route::get('fetch/report/detail_stat', 'EmployeeController@detailReport');
Route::get('index/report/leave_control', 'AbsenceController@indexReportLeaveControl');

//OVERTIME
Route::get('fetch/report/overtime_section', 'OvertimeController@fetchReportOvertimeSection');
Route::get('index/report/overtime_monthly_fq', 'OvertimeController@indexReportControlFq');
// Route::get('index/report/overtime_monthly', 'OvertimeController@indexReportControl');
Route::get('index/report/overtime_monthly_bdg', 'OvertimeController@indexReportControlBdg');
Route::get('index/report/overtime_section', 'OvertimeController@indexReportSection');
Route::get('fetch/report/overtime_report_section', 'OvertimeController@fetchReportSection');
Route::get('index/report/overtime_data', 'OvertimeController@indexOvertimeData');
Route::get('fetch/report/overtime_data', 'OvertimeController@fetchOvertimeData');
Route::get('index/report/overtime_outsource', 'OvertimeController@indexReportOutsouce');
Route::get('fetch/report/overtime_report_outsource', 'OvertimeController@fetchReportOutsource');
Route::get('fetch/report/overtime_detail_outsource', 'OvertimeController@fetchDetailOutsource');
Route::get('index/report/overtime_outsource_data', 'OvertimeController@indexOvertimeOutsource');
Route::get('fetch/report/overtime_data_outsource', 'OvertimeController@fetchOvertimeDataOutsource');
Route::get('index/report/overtime_by_employee', 'OvertimeController@indexOvertimeByEmployee');
Route::get('fetch/report/overtime_by_employee', 'OvertimeController@fetchOvertimeByEmployee');
Route::get('fetch/report/detail_ot_by_employee', 'OvertimeController@detailOvertimeByEmployee');
Route::get('index/report/overtime_resume', 'OvertimeController@indexMonthlyResume');
Route::get('fetch/report/overtime_resume', 'OvertimeController@fetchMonthlyResume');

Route::get('index/report/ga_report', 'OvertimeController@indexGAReport');
Route::get('fetch/report/ga_report', 'OvertimeController@fetchGAReport');

Route::group(['nav' => 'R9', 'middleware' => 'permission'], function(){
	Route::get('index/report/overtime_control', 'OvertimeController@indexOvertimeControl');
	Route::get('fetch/report/overtime_control', 'OvertimeController@fetchOvertimeControl');
});

Route::get('fetch/overtime_report', 'OvertimeController@overtimeReport');
Route::get('fetch/overtime_report_detail', 'OvertimeController@overtimeReportDetail');
Route::get('index/report/total_meeting', 'EmployeeController@indexTotalMeeting');
Route::get('fetch/report/total_meeting', 'EmployeeController@fetchTotalMeeting');
Route::get('fetch/report/gender', 'EmployeeController@fetchReportGender');
Route::get('fetch/report/status1', 'EmployeeController@fetchReportStatus');
Route::get('fetch/report/serikat', 'EmployeeController@reportSerikat');
Route::get('fetch/report/overtime_report_control', 'OvertimeController@overtimeControl');
Route::get('fetch/overtime_report_over', 'OvertimeController@overtimeOver');
Route::get('index/employee/service', 'EmployeeController@indexEmployeeService')->name('emp_service');
Route::get('fetch/report/kaizen', 'EmployeeController@fetchKaizen');
Route::get('fetch/sub_leader', 'EmployeeController@fetchSubLeader');
Route::get('create/ekaizen/{id}/{name}/{section}/{group}', 'EmployeeController@makeKaizen');
Route::get('create/ekaizen/{id}/{name}/{section}', 'EmployeeController@makeKaizen2');
Route::post('post/ekaizen', 'EmployeeController@postKaizen');
Route::post('update/ekaizen', 'EmployeeController@updateKaizen');
Route::get('get/ekaizen', 'EmployeeController@getKaizen');
Route::get('index/updateKaizen/{id}', 'EmployeeController@indexUpdateKaizenDetail');
Route::get('delete/kaizen', 'EmployeeController@deleteKaizen');
Route::get('fetch/chat/hrqa', 'EmployeeController@fetchChat');
Route::post('post/chat/comment', 'EmployeeController@postComment');
Route::post('post/hrqa', 'EmployeeController@postChat');
Route::get('index/employee_information', 'EmployeeController@indexEmployeeInformation');
Route::get('fetch/cc/budget', 'OvertimeController@fetchCostCenterBudget');
Route::get('fetch/chart/control/detail', 'OvertimeController@overtimeDetail');
Route::get('update/employee/number', 'EmployeeController@editNumber');
// DailyAttendanceControl
Route::get('index/report/daily_attendance', 'EmployeeController@indexDailyAttendance');
Route::get('fetch/report/daily_attendance', 'EmployeeController@fetchDailyAttendance');
Route::get('fetch/report/detail_daily_attendance', 'EmployeeController@detailDailyAttendance');
Route::get('index/report/attendance_data', 'EmployeeController@attendanceData');
Route::get('fetch/report/attendance_data', 'EmployeeController@fetchAttendanceData');
// Presence
Route::get('index/report/presence', 'EmployeeController@indexPresence');
Route::get('fetch/report/presence', 'EmployeeController@fetchPresence');
Route::get('fetch/report/detail_presence', 'EmployeeController@detailPresence');
// Absence
Route::get('index/report/absence', 'EmployeeController@indexAbsence');
Route::get('fetch/report/absence', 'EmployeeController@fetchAbsence');
Route::get('fetch/report/detail_absence', 'EmployeeController@detailAbsence');
Route::get('fetch/absence/employee', 'EmployeeController@fetchAbsenceEmployee');

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
	Route::get('index/fg_production_monitoring', 'ProductionScheduleController@indexProductionMonitoring');
});

Route::get('index/fg_production_schedule', 'ProductionScheduleController@indexProductionData');
Route::get('fetch/fg_production_schedule', 'ProductionScheduleController@fetchProductionData');

Route::get('index/production_achievement', 'ChoreiController@indexProductionAchievement');
Route::get('fetch/production_achievement', 'ChoreiController@fetchProductionAchievement');


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
	Route::get('index/middle/buffing_adjustment', 'MiddleProcessController@indexBuffingAdjustment');
	Route::get('fetch/middle/buffing_adjustment', 'MiddleProcessController@fetchBuffingAdjustment');
	Route::post('post/middle/buffing_delete_queue', 'MiddleProcessController@deleteBuffingQueue');
	Route::post('post/middle/buffing_add_queue', 'MiddleProcessController@addBuffingQueue');
	Route::get('index/middle/wip_adjustment', 'MiddleProcessController@indexWIPAdjustment');
	Route::get('fetch/middle/barrel_adjustment', 'MiddleProcessController@fetchBarrelAdjustment');
	Route::get('fetch/middle/barrel_inactive/{id}', 'MiddleProcessController@fetchBarrelInactive');
	Route::get('fetch/middle/wip', 'MiddleProcessController@fetchWIP');
	Route::post('post/middle/barrel_inactive', 'MiddleProcessController@postInactive');
	Route::post('post/middle/barrel_inactive_wip', 'MiddleProcessController@postInactiveWIP');
	Route::post('post/middle/new/barrel_inactive', 'MiddleProcessController@CreateInactive');
	Route::post('import/barrel_inactive', 'MiddleProcessController@importInactive');
});

Route::group(['nav' => 'A9', 'middleware' => 'permission'], function(){
	Route::get('index/middle/buffing_canceled', 'MiddleProcessController@indexBuffingCanceled');
	Route::get('fetch/middle/buffing_canceled', 'MiddleProcessController@fetchBuffingCanceled');
	Route::post('delete/middle/buffing_canceled', 'MiddleProcessController@deleteBuffingCanceled');

	Route::get('index/middle/buffing_target/{loc}', 'MiddleProcessController@indexBuffingTarget');
	Route::get('fetch/middle/buffing_target/{loc}', 'MiddleProcessController@fetchBuffingTarget');
	Route::post('update/middle/buffing_target', 'MiddleProcessController@updateBuffingTarget');

	Route::post('update/middle/buffing_op_eff_check', 'MiddleProcessController@updateEffCheck');
	Route::post('update/middle/buffing_op_ng_check', 'MiddleProcessController@updateNgCheck');
});

Route::get('setting/user', 'UserController@index_setting');
Route::post('setting/user', 'UserController@setting');
	// Route::get('register', 'UserController@indexRegister');
	// Route::post('register', 'UserController@register');
Route::post('register', 'RegisterController@register')->name('register');

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
	// Route::get('create/material', 'MaterialController@create');
	Route::get('fetch/material', 'MaterialController@fetchMaterial');
	Route::post('create/material', 'MaterialController@create');
	Route::post('delete/material', 'MaterialController@delete');
	Route::get('edit/material', 'MaterialController@fetchEdit');
	Route::post('edit/material', 'MaterialController@edit');
	Route::get('view/material', 'MaterialController@view');
	Route::post('import/material', 'MaterialController@import');
});

Route::get('index/material/request', 'MaterialController@indexMaterialRequest');
Route::get('fetch/material/request_list', 'MaterialController@fetchMaterialRequestList');
Route::get('index/material/receive', 'MaterialController@indexMaterialReceive');
Route::get('index/material/data', 'MaterialController@indexMaterialData');

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
	Route::get('fetch/production_schedule', 'ProductionScheduleController@fetchSchedule');
	// Route::get('create/production_schedule', 'ProductionScheduleController@create');
	Route::post('create/production_schedule', 'ProductionScheduleController@store');
	Route::get('destroy/production_schedule', 'ProductionScheduleController@destroy');
	Route::post('delete/production_schedule', 'ProductionScheduleController@delete');
	Route::get('edit/production_schedule', 'ProductionScheduleController@fetchEdit');
	Route::post('edit/production_schedule', 'ProductionScheduleController@edit');
	Route::get('view/production_schedule', 'ProductionScheduleController@show');
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
	Route::get('fetch/shipment_schedule', 'ShipmentScheduleController@fetchShipment');
	// Route::get('create/shipment_schedule', 'ShipmentScheduleController@create');
	Route::post('create/shipment_schedule', 'ShipmentScheduleController@create');
	Route::get('view/shipment_schedule', 'ShipmentScheduleController@view');
	Route::get('delete/shipment_schedule', 'ShipmentScheduleController@delete');
	Route::get('edit/shipment_schedule', 'ShipmentScheduleController@fetchEdit');
	Route::post('edit/shipment_schedule', 'ShipmentScheduleController@edit');
	// Route::get('show/shipment_schedule/{id}', 'ShipmentScheduleController@show');
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
	Route::get('fetch/cost_center', 'EmployeeController@getCostCenter');

	//insert
	Route::get('index/insertEmp', 'EmployeeController@insertEmp');
	Route::get('index/updateEmp/{nik}', 'EmployeeController@updateEmp');

	//import
	Route::post('import/importEmp', 'EmployeeController@importEmp');
	Route::post('import/bagian', 'EmployeeController@importBagian');
	Route::post('import/employee', 'EmployeeController@importKaryawan');
});


Route::group(['nav' => 'M17', 'middleware' => 'permission'], function(){
	Route::get('index/assy_schedule', 'AssyProcessController@indexSchedule');
	Route::get('fetch/assy_schedule', 'AssyProcessController@fetchSchedule');
	Route::post('create/assy_schedule', 'AssyProcessController@createSchedule');
	Route::post('delete/assy_schedule', 'AssyProcessController@delete');
	Route::post('edit/assy_schedule', 'AssyProcessController@edit');
	Route::get('edit/assy_schedule', 'AssyProcessController@fetchEdit');
	Route::get('destroy/assy_schedule', 'AssyProcessController@destroy');
	Route::get('view/assy_schedule', 'AssyProcessController@view');
	Route::post('import/assy_schedule', 'AssyProcessController@import');
});

Route::group(['nav' => 'M18', 'middleware' => 'permission'], function(){
	Route::get('index/safety_stock', 'InitialProcessController@indexStockMaster');
	Route::get('fetch/safety_stock', 'InitialProcessController@fetchStockMaster');
	Route::get('view/safety_stock', 'InitialProcessController@view');
	Route::post('edit/safety_stock', 'InitialProcessController@edit');
	Route::get('edit/safety_stock', 'InitialProcessController@fetchEdit');
	Route::post('import/safety_stock', 'InitialProcessController@import');
	Route::post('create/safety_stock', 'InitialProcessController@createInitial');
	Route::post('delete/safety_stock', 'InitialProcessController@delete');
	Route::get('destroy/safety_stock', 'InitialProcessController@destroy');
});

Route::group(['nav' => 'M19', 'middleware' => 'permission'], function(){
	Route::post('import/material/storage', 'RawMaterialController@importStorage');
	Route::post('import/material/smbmr', 'RawMaterialController@importSmbmr');
	Route::post('calculate/material/usage', 'RawMaterialController@calculateUsage');
});
Route::get('index/material/usage', 'RawMaterialController@indexUsage');
Route::get('fetch/material/usage', 'RawMaterialController@fetchUsage');
Route::get('index/material/smbmr', 'RawMaterialController@indexSmbmr');
Route::get('fetch/material/smbmr', 'RawMaterialController@fetchSmbmr');
Route::get('index/material/storage', 'RawMaterialController@indexStorage');
Route::get('fetch/material/storage', 'RawMaterialController@fetchStorage');
Route::get('index/material/monitoring', 'InitialProcessController@indexMonitoring');

Route::group(['nav' => 'M20', 'middleware' => 'permission'], function(){
	Route::get('index/user_document', 'UserDocumentController@index');
	Route::get('fetch/user_document', 'UserDocumentController@fetchUserDocument');
	Route::get('fetch/user_document_detail', 'UserDocumentController@fetchUserDocumentDetail');
	Route::post('fetch/user_document_renew', 'UserDocumentController@fetchUserDocumentRenew');
	Route::post('fetch/user_document_update', 'UserDocumentController@fetchUserDocumentUpdate');
	Route::post('fetch/user_document_create', 'UserDocumentController@fetchUserDocumentCreate');	
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


Route::group(['nav' => 'S37', 'middleware' => 'permission'], function(){
	Route::post('print/return', 'TransactionController@printReturn');
	Route::get('reprint/return', 'TransactionController@reprintReturn');
	Route::post('confirm/return', 'TransactionController@confirmReturn');
});
Route::get('index/return', 'TransactionController@indexReturn');
Route::get('index/return/data', 'TransactionController@indexReturnData');
Route::get('fetch/return/data', 'TransactionController@fetchReturnData');
Route::get('fetch/return/list', 'TransactionController@fetchReturnList');
Route::get('fetch/return', 'TransactionController@fetchReturn');
Route::get('fetch/return/resume', 'TransactionController@fetchReturnResume');

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
	// return sax
	Route::post('returnfg/stamp', 'ProcessController@returnfgStamp');
	Route::post('scan/serial_number_return_Sx', 'ProcessController@scanSerialNumberReturnSx');
	// end return sax

	// return cl
	Route::post('returncl/stamp', 'ProcessController@returnclStamp');
	Route::post('scan/serial_number_return_Cl', 'ProcessController@scanSerialNumberReturnCl');
	// end return cl

	// ng sax
	Route::post('ngfg/stamp', 'ProcessController@ngsxStamp');
	Route::post('scan/serial_number_ng_Sx', 'ProcessController@scanSerialNumberngSx');
	// end ng sax

	// ng FL
	Route::post('ngfgFL/stamp', 'ProcessController@ngFLStamp');
	Route::post('scan/serial_number_ng_FL', 'ProcessController@scanSerialNumberngFL');
	// end ng FL
});

//meeting
Route::group(['nav' => 'S33', 'middleware' => 'permission'], function(){
	Route::get('index/meeting/create', 'MeetingController@create');
	Route::get('index/meeting/list/{id}', 'MeetingController@list');
	Route::post('delete/meeting', 'MeetingController@deleteMeeting');
	Route::post('edit/meeting', 'MeetingController@editMeeting');
	Route::post('create/meeting', 'MeetingController@createMeeting');
	Route::post('scan/meeting/attendance', 'MeetingController@scanMeetingAttendance');
	Route::get('fetch/meeting/add_participant', 'MeetingController@fetchAddParticipant');
	Route::get('download/meeting', 'MeetingController@downloadMeeting');
});
Route::get('index/meeting', 'MeetingController@indexMeeting');
Route::get('index/meeting/attendance', 'MeetingController@indexMeetingAttendance');
Route::get('fetch/meeting', 'MeetingController@fetchMeeting');
Route::get('fetch/meeting/group', 'MeetingController@fetchMeetingGroup');
Route::get('fetch/meeting/detail', 'MeetingController@fetchMeetingDetail');
Route::get('fetch/meeting/attendance', 'MeetingController@fetchMeetingAttendance');

//welding process
Route::group(['nav' => 'S32', 'middleware' => 'permission'], function(){
	Route::post('input/welding/rework', 'WeldingProcessController@inputWeldingRework');
	Route::post('input/welding/kensa', 'WeldingProcessController@inputWeldingKensa');
});

Route::get('index/welding/operator', 'WeldingProcessController@indexMasterOperator');
Route::get('fetch/welding/operator', 'WeldingProcessController@fetchMasterOperator');
Route::post('post/welding/add_operator', 'WeldingProcessController@addOperator');
Route::post('post/welding/add_kanban', 'WeldingProcessController@addKanban');
Route::get('index/welding/destroy_operator/{id}', 'WeldingProcessController@destroyOperator');
Route::get('fetch/welding/get_operator', 'WeldingProcessController@getOperator');
Route::post('index/welding/update_operator', 'WeldingProcessController@updateOperator');
Route::get('index/welding/display_production_result', 'WeldingProcessController@indexDisplayProductionResult');
Route::get('fetch/welding/display_production_result', 'WeldingProcessController@fetchDisplayProductionResult');
Route::get('index/welding/report_ng', 'WeldingProcessController@indexReportNG');
Route::get('fetch/welding/report_ng', 'WeldingProcessController@fetchReportNG');
Route::get('index/welding/report_hourly', 'WeldingProcessController@indexReportHourly');
Route::get('fetch/welding/report_hourly', 'WeldingProcessController@fetchReportHourly');
Route::get('index/welding/ng_rate', 'WeldingProcessController@indexNgRate');
Route::get('fetch/welding/ng_rate', 'WeldingProcessController@fetchNgRate');
Route::get('index/welding/op_ng', 'WeldingProcessController@indexOpRate');
Route::get('fetch/welding/op_ng', 'WeldingProcessController@fetchOpRate');
Route::get('fetch/welding/op_ng_detail', 'WeldingProcessController@fetchOpRateDetail');
Route::get('index/welding/op_analysis', 'WeldingProcessController@indexOpAnalysis');
Route::get('fetch/welding/op_analysis', 'WeldingProcessController@fetchOpAnalysis');
Route::get('index/welding/welding_op_eff', 'WeldingProcessController@indexWeldingOpEff');
Route::get('fetch/welding/welding_op_eff', 'WeldingProcessController@fetchWeldingOpEff');
Route::get('index/welding/welding_eff', 'WeldingProcessController@indexWeldingEff');
Route::get('fetch/welding/welding_op_eff_ongoing', 'WeldingProcessController@fetchWeldingEffOngoing');
Route::get('fetch/welding/welding_op_eff_target', 'WeldingProcessController@fetchWeldingOpEffTarget');
Route::get('index/welding/production_result', 'WeldingProcessController@indexProductionResult');
Route::get('fetch/welding/production_result', 'WeldingProcessController@fetchProductionResult');
Route::get('index/welding/kensa/{id}', 'WeldingProcessController@indexWeldingKensa');
Route::get('scan/welding/operator', 'WeldingProcessController@scanWeldingOperator');
Route::get('scan/welding/kensa', 'WeldingProcessController@scanWeldingKensa');
Route::get('fetch/welding/kensa_result', 'WeldingProcessController@fetchKensaResult');
Route::get('index/welding/resume/{id}', 'WeldingProcessController@indexWeldingResume');
Route::get('fetch/welding/resume', 'WeldingProcessController@fetchWeldingResume');
Route::get('fetch/welding/key_resume', 'WeldingProcessController@fetchWeldingKeyResume');
Route::get('fetch/welding/ng_resume', 'WeldingProcessController@fetchWeldingKeyResume');
Route::get('index/welding/group_achievement', 'WeldingProcessController@indexWeldingAchievement');
Route::get('fetch/welding/group_achievement', 'WeldingProcessController@fetchGroupAchievement');
Route::get('fetch/welding/group_achievement_detail', 'WeldingProcessController@fetchGroupAchievementDetail');
Route::get('fetch/welding/accumulated_achievement', 'WeldingProcessController@fetchAccumulatedAchievement');
Route::get('index/welding/eff_handling', 'WeldingProcessController@indexEffHandling');
Route::get('fetch/welding/eff_handling', 'WeldingProcessController@fetchEffHandling');

Route::get('index/welding/welding_adjustment', 'WeldingProcessController@indexWeldingAdjustment');
Route::get('fetch/welding/welding_queue', 'WeldingProcessController@fetchWeldingQueue');
Route::get('fetch/welding/welding_stock', 'WeldingProcessController@fetchWeldingStock');
Route::post('post/welding/welding_add_queue', 'WeldingProcessController@inputWeldingQueue');
Route::post('post/welding/welding_delete_queue', 'WeldingProcessController@deleteWeldingQueue');


Route::get('index/welding/welding_board/{loc}', 'WeldingProcessController@indexWeldingBoard');
Route::get('fetch/welding/welding_board', 'WeldingProcessController@fetchWeldingBoard');
Route::get('fetch/welding/fetch_detail', 'WeldingProcessController@fetchDetailWeldingBoard');
Route::get('index/welding/master_kanban/{loc}', 'WeldingProcessController@indexMasterKanban');
Route::get('fetch/welding/kanban', 'WeldingProcessController@fetchMasterKanban');
Route::get('fetch/welding/show_edit_kanban', 'WeldingProcessController@fetchShowEdit');
Route::post('post/welding/edit_kanban', 'WeldingProcessController@editKanban');


Route::get('index/welding/destroy_kanban/{loc}/{id}', 'WeldingProcessController@destroyKanban');
Route::get('index/welding/current_welding', 'WeldingProcessController@indexCurrentWelding');
Route::get('fetch/welding/current_welding', 'WeldingProcessController@fetchCurrentWelding');
Route::get('index/welding/op_trend', 'WeldingProcessController@indexWeldingTrend');
Route::get('fetch/welding/op_trend', 'WeldingProcessController@fetchWeldingTrend');

//JIG
Route::group(['nav' => 'M27', 'middleware' => 'permission'], function(){

});
Route::get('index/welding_jig', 'WeldingProcessController@indexWeldingJig');
Route::get('index/welding/jig/data', 'WeldingProcessController@indexWeldingJigData');
Route::get('index/welding/kensa_jig', 'WeldingProcessController@indexWeldingKensaJig');

Route::group(['nav' => 'M28', 'middleware' => 'permission'], function(){
	Route::get('index/supplier', 'AccountingController@master_supplier');
	Route::get('fetch/supplier', 'AccountingController@fetch_supplier');
});

Route::group(['nav' => 'M29', 'middleware' => 'permission'], function(){
	Route::get('index/purchase_item', 'AccountingController@master_item');
	Route::get('fetch/purchase_item', 'AccountingController@fetch_item');
});

Route::get('purchase_requisition', 'AccountingController@purchase_requisition');
Route::get('fetch/purchase_requisition', 'AccountingController@fetch_purchase_requisition');
Route::post('create/purchase_requisition', 'AccountingController@create_purchase_requisition');

Route::get('investment', 'AccountingController@investment');
Route::get('fetch/investment', 'AccountingController@fetch_investment');
Route::get('investment/create', 'AccountingController@create_investment');
Route::post('investment/create_post', 'AccountingController@create_investment_post');
Route::get('investment/detail/{id}', 'AccountingController@detail_investment');
Route::post('investment/update_post', 'AccountingController@detail_investment_post');

Route::post('investment/create_investment_item', 'AccountingController@create_investment_item');
Route::get('investment/fetch_investment_item/{id}', 'AccountingController@fetch_investment_item');
Route::post('investment/edit_investment_item', 'AccountingController@edit_investment_item');
Route::get('investment/edit_investment_item', 'AccountingController@fetch_investment_item_edit');
Route::post('investment/delete_investment_item', 'AccountingController@delete_investment_item');
Route::get('investment/get_detailitem', 'AccountingController@getitemdesc')->name('admin.getitemdesc');

Route::group(['nav' => 'S12', 'middleware' => 'permission'], function(){
	Route::get('scan/middle/kensa', 'MiddleProcessController@ScanMiddleKensa');
	Route::get('scan/middle/operator', 'MiddleProcessController@scanMiddleOperator');
	Route::post('input/middle/kensa', 'MiddleProcessController@inputMiddleKensa');
	Route::post('input/middle/rework', 'MiddleProcessController@inputMiddleRework');
	// Route::post('input/result_middle_kensa', 'MiddleProcessController@inputResultMiddleKensa');
	Route::post('print/middle/barrel', 'MiddleProcessController@printMiddleBarrel');
	Route::post('scan/middle/barrel', 'MiddleProcessController@scanMiddleBarrel');
	Route::post('post/middle_return/barrel_return', 'MiddleProcessController@postProcessMiddleReturn');
	Route::post('post/middle_return/return_inventory', 'MiddleProcessController@postReturnInventory');
	Route::get('print/middle/barrel_reprint', 'MiddleProcessController@printMiddleBarrelReprint');
});

//KD
Route::group(['nav' => 'S24', 'middleware' => 'permission'], function(){
	Route::get('index/kd_zpro/{id}', 'KnockDownController@indexKD');
	Route::post('fetch/kd_print_zpro', 'KnockDownController@printLabel');	
	Route::post('fetch/kd_force_print_zpro', 'KnockDownController@forcePrintLabel');

	Route::get('index/print_label_zpro/{id}', 'KnockDownController@indexPrintLabelZpro');

});

Route::group(['nav' => 'S25', 'middleware' => 'permission'], function(){
	Route::get('index/kd_subassy_sx/{id}', 'KnockDownController@indexKD');
});

Route::group(['nav' => 'S26', 'middleware' => 'permission'], function(){
	Route::get('index/kd_subassy_fl/{id}', 'KnockDownController@indexKD');
});

Route::group(['nav' => 'S27', 'middleware' => 'permission'], function(){
	Route::get('index/kd_subassy_cl/{id}', 'KnockDownController@indexKD');
});

Route::group(['nav' => 'S29', 'middleware' => 'permission'], function(){
	Route::get('index/kd_delivery', 'KnockDownController@indexKdDelivery');
	Route::post('scan/kd_delivery', 'KnockDownController@scanKdDelivery');
	Route::get('index/kd_stuffing', 'KnockDownController@indexKdStuffing');
	Route::post('scan/kd_stuffing', 'KnockDownController@scanKdStuffing');
	Route::post('delete/kdo_stuffing', 'KnockDownController@deleteKdStuffing');
	Route::post('delete/kdo_delivery', 'KnockDownController@deleteKdDelivery');	
	Route::post('delete/kdo', 'KnockDownController@deleteKd');
	Route::post('delete/kdo_detail', 'KnockDownController@deleteKdDetail');
});
Route::get('fetch/kd/{id}', 'KnockDownController@fetchKd');
Route::get('fetch/kd_pack/{id}', 'KnockDownController@fetchKdPack');
Route::get('fetch/kd_detail', 'KnockDownController@fetchKdDetail');
Route::get('index/kd_daily_production_result', 'KnockDownController@indexKdDailyProductionResult');
Route::get('fetch/kd_daily_production_result', 'KnockDownController@fetchKdDailyProductionResult');
Route::get('index/kd_production_schedule_data', 'KnockDownController@indexKdProductionScheduleData');
Route::get('fetch/kd_production_schedule_data', 'KnockDownController@fetchKdProductionScheduleData');
Route::get('index/kd_stock', 'KnockDownController@indexKdStock');
Route::get('fetch/kd_stock', 'KnockDownController@fetchKdStock');
Route::get('fetch/kd_stock_detail', 'KnockDownController@fetchKdStockDetail');
Route::get('index/kd_shipment_progress', 'KnockDownController@indexKdShipmentProgress');
Route::get('fetch/kd_shipment_progress', 'KnockDownController@fetchKdShipmentProgress');
Route::get('fetch/kd_shipment_progress_detail', 'KnockDownController@fetchKdShipmentProgressDetail');
Route::get('fetch/kdo', 'KnockDownController@fetchKDO');
Route::get('fetch/kdo_detail', 'KnockDownController@fetchKDODetail');

Route::group(['nav' => 'S30', 'middleware' => 'permission'], function(){
	Route::get('index/workshop/list_wjo', 'WorkshopController@indexListWJO');
	Route::post('update/workshop/wjo', 'WorkshopController@updateWJO');
	Route::post('edit/workshop/wjo', 'WorkshopController@editLeaderWJO');
	Route::post('check/workshop/wjo_rfid', 'WorkshopController@checkTag');
	Route::post('reject/workshop/wjo', 'WorkshopController@rejectWJO');
	Route::post('close/workshop/wjo', 'WorkshopController@closeWJO');
	Route::get('index/workshop/drawing', 'WorkshopController@indexDrawing');
	Route::post('create/workshop/drawing', 'WorkshopController@createDrawing');
	Route::post('edit/workshop/drawing', 'WorkshopController@editDrawing');
	Route::get('index/workshop/job_history', 'WorkshopController@indexJobHistory');
	Route::get('fetch/workshop/job_history', 'WorkshopController@fetchJobHistory');
	Route::get('excel/workshop/job_history', 'WorkshopController@exportJobHistory');	
	Route::get('index/workshop/receipt', 'WorkshopController@indexWJOReceipt');
	Route::get('fetch/workshop/receipt', 'WorkshopController@fetchFinishedWJO');
	Route::get('fetch/workshop/receipt/after', 'WorkshopController@fetchReceivedWJO');
	Route::get('fetch/workshop/picked', 'WorkshopController@fetchPickedWJO');
	Route::get('scan/workshop/receipt', 'WorkshopController@scanWJOReceipt');
});

Route::group(['nav' => 'S31', 'middleware' => 'permission'], function(){
	Route::get('index/workshop/wjo', 'WorkshopController@indexWJO');
});

Route::get('index/workshop/workload', 'WorkshopController@indexWorkload');
Route::get('fetch/workshop/workload', 'WorkshopController@fetchWorkload');
Route::get('fetch/workshop/workload_operator_detail', 'WorkshopController@fetchWorkloadOperatorDetail');

Route::get('index/workshop/operatorload', 'WorkshopController@indexOperatorload');
Route::get('fetch/workshop/operatorload', 'WorkshopController@fetchOperatorload');


Route::get('fetch/workshop/machine', 'WorkshopController@scanMachine');
Route::get('index/workshop/create_wjo', 'WorkshopController@indexCreateWJO');
Route::post('create/workshop/wjo', 'WorkshopController@createWJO');
Route::get('cancel/workshop/wjo', 'WorkshopController@cancelWJO');
Route::get('index/workshop/edit_wjo', 'WorkshopController@fetch_item_edit');
Route::post('index/workshop/edit_wjo', 'WorkshopController@editWJO');
Route::get('update/workshop/approve_urgent/{id}', 'WorkshopNotificationController@approveUrgent');
Route::get('update/workshop/reject_urgent/{id}', 'WorkshopNotificationController@rejectUrgent');
Route::get('fetch/workshop/list_wjo', 'WorkshopController@fetchListWJO');
Route::get('fetch/workshop/assign_form', 'WorkshopController@fetchAssignForm');
Route::get('export/workshop/list_wjo', 'WorkshopController@exportListWJO');
Route::get('download/workshop/{id}', 'WorkshopController@downloadAttachment');
Route::get('scan/workshop/operator/rfid', 'WorkshopController@scanOperator');
Route::get('scan/workshop/tag/rfid', 'WorkshopController@scanTag');
Route::get('scan/workshop/leader/rfid', 'WorkshopController@scanLeader');
Route::post('create/workshop/tag/process_log', 'WorkshopController@createProcessLog');
Route::get('close/workshop/check_rfid', 'WorkshopController@checkCloseTag');
Route::get('fetch/workshop/drawing', 'WorkshopController@fetchDrawing');
Route::get('fetch/workshop/edit_drawing', 'WorkshopController@fetchEditDrawing');
Route::get('index/workshop/wjo_monitoring', 'WorkshopController@indexWJOMonitoring');
Route::get('fetch/workshop/wjo_monitoring', 'WorkshopController@fetchWJOMonitoring');
Route::get('index/workshop/productivity', 'WorkshopController@indexProductivity');
Route::get('fetch/workshop/productivity', 'WorkshopController@fetchProductivity');
Route::get('fetch/workshop/operator_detail', 'WorkshopController@fetchOperatorDetail');
Route::get('fetch/workshop/machine_detail', 'WorkshopController@fetchmachineDetail');
Route::get('fetch/workshop/process_detail', 'WorkshopController@fetchProcessDetail');
Route::get('fetch/workshop/drawingMaterial', 'WorkshopController@fetchDrawingMaterial');

Route::get('index/middle/op_analysis', 'MiddleProcessController@indexOpAnalysis');
Route::get('fetch/middle/op_analysis', 'MiddleProcessController@fetchOpAnalysis');
Route::get('fetch/middle/op_analysis_detail', 'MiddleProcessController@fetchOpAnalysisDetail');


Route::get('fetch/middle/kensa', 'MiddleProcessController@fetchMiddleKensa');
Route::get('scan/middle/buffing/kensa/material', 'MiddleProcessController@fetchBuffing');
Route::get('scan/middle/operator/rfid', 'MiddleProcessController@scanMiddleOperatorKensa');
Route::get('index/process_middle_sx', 'MiddleProcessController@indexProcessMiddleSX');
Route::get('index/middle/request/{id}', 'MiddleProcessController@indexRequest');
Route::get('index/middle/request/display/{id}', 'MiddleProcessController@indexRequestDisplay');
Route::get('fetch/middle/request', 'MiddleProcessController@fetchRequest');
//FROM SOLDERING
Route::get('index/middle/requested', 'MiddleProcessController@indexRequestSolder');
//CLARINET
Route::get('index/process_middle_cl', 'MiddleProcessController@indexProcessMiddleCL');
Route::get('scan/middle/request', 'MiddleProcessController@scanRequestTag');
//FLUTE
Route::get('index/process_middle_fl', 'MiddleProcessController@indexProcessMiddleFL');
Route::get('index/middle/request_fl', 'MiddleProcessController@indexRequestFL');
Route::get('index/process_middle_kensa/{id}', 'MiddleProcessController@indexProcessMiddleKensa');
Route::get('index/process_middle_barrel/{id}', 'MiddleProcessController@indexProcessMiddleBarrel');
Route::get('fetch/middle/barrel', 'MiddleProcessController@fetchMiddleBarrel');
Route::get('fetch/middle/barrel_machine', 'MiddleProcessController@fetchMiddleBarrelMachine');
Route::get('index/middle/barrel_machine', 'MiddleProcessController@indexProcessBarrelMachine');
Route::get('index/middle/barrel_board/{id}', 'MiddleProcessController@indexProcessBarrelBoard');
Route::get('index/middle/buffing_board/{id}', 'MiddleProcessController@indexBuffingBoard');
Route::get('index/middle/buffing_board_reverse/{id}', 'MiddleProcessController@indexBuffingBoardReverse');
Route::get('fetch/middle/get_barrel_machine', 'MiddleProcessController@fetchProcessBarrelMachine');
Route::get('fetch/middle/get_barrel', 'MiddleProcessController@fetchProcessBarrel');
Route::get('fetch/middle/barrel_board', 'MiddleProcessController@fetchMiddleBarrelBoard');
Route::get('fetch/middle/barrel_machine_status', 'MiddleProcessController@fetchMachine');
Route::get('index/process_middle_return/{id}', 'MiddleProcessController@indexProcessMiddleReturn');
Route::get('fetch/middle_return/barrel_return', 'MiddleProcessController@fetchProcessMiddleReturn');
Route::get('fetch/middle/barrel_reprint', 'MiddleProcessController@fetchMiddleBarrelReprint');
Route::get('fetch/middle/barrel_result', 'MiddleProcessController@fetchBarrelBoardDetails');
Route::get('index/report_middle/{id}', 'MiddleProcessController@indexReportMiddle');
Route::get('fetch/middle/buffing_board', 'MiddleProcessController@fetchBuffingBoard');
Route::get('fetch/middle/buffing_board_reverse', 'MiddleProcessController@fetchBuffingReverse');
Route::get('index/middle/barrel_log', 'MiddleProcessController@indexBarrelLog');
Route::get('fetch/middle/barrel_log', 'MiddleProcessController@fetchBarrelLog');
Route::get('index/middle/report_ng', 'MiddleProcessController@indexReportNG');
Route::get('index/middle/report_production_result', 'MiddleProcessController@indexReportProductionResult');
Route::get('fetch/middle/report_ng', 'MiddleProcessController@fetchReportNG');
Route::get('fetch/middle/report_production_result', 'MiddleProcessController@fetchReportProductionResult');
Route::get('index/middle/display_production_result', 'MiddleProcessController@indexDisplayProductionResult');
Route::get('fetch/middle/display_production_result', 'MiddleProcessController@fetchDisplayProductionResult');
Route::get('index/process_buffing_kensa/{id}', 'MiddleProcessController@indexProcessBuffingKensa');
Route::post('input/middle/buffing/kensa', 'MiddleProcessController@inputBuffingKensa');
Route::get('index/middle/display_picking', 'MiddleProcessController@indexDisplayPicking');
Route::get('fetch/middle/display_picking', 'MiddleProcessController@fetchDisplayPicking');
Route::get('index/middle/display_monitoring', 'MiddleProcessController@indexDisplayMonitoring');
Route::get('fetch/middle/display_monitoring', 'MiddleProcessController@fetchDisplayMonitoring');
Route::get('fetch/middle/detail_monitoring', 'MiddleProcessController@fetchDetailStockMonitoring');
Route::get('index/middle/detail_monitoring', 'MiddleProcessController@fetchDetailStockMonitoring');

// Report Middle Global
Route::get('index/middle/display_kensa_time', 'MiddleProcessController@indexDisplayKensaTime');
Route::get('fetch/middle/display_kensa_time', 'MiddleProcessController@fetchDisplayKensaTime');
// Report Middle PLT
Route::get('index/middle/report_plt_ng/{id}', 'MiddleProcessController@indexReportPltNg');
Route::get('fetch/middle/plt_ng_rate_monthly/{id}', 'MiddleProcessController@fetchPltNgRateMonthly');
Route::get('fetch/middle/plt_ng_rate_weekly/{id}', 'MiddleProcessController@fetchPltNgRateWeekly');
Route::get('fetch/middle/plt_ng/{id}', 'MiddleProcessController@fetchPltNg');
Route::get('fetch/middle/plt_ng_rate/{id}', 'MiddleProcessController@fetchPltNgRate');

// Report Middle LCQ
Route::get('index/middle/report_lcq_ng', 'MiddleProcessController@indexReportLcqNg');
Route::get('fetch/middle/lcq_ng_rate_monthly', 'MiddleProcessController@fetchLcqNgRateMonthly');
Route::get('fetch/middle/lcq_ng_rate_weekly', 'MiddleProcessController@fetchLcqNgRateWeekly');
Route::get('fetch/middle/lcq_ng', 'MiddleProcessController@fetchLcqNg');
Route::get('fetch/middle/lcq_ng_rate', 'MiddleProcessController@fetchLcqNgRate');
Route::get('index/middle/report_hourly_lcq', 'MiddleProcessController@indexReportHourlyLcq');
Route::get('fetch/middle/report_hourly_lcq', 'MiddleProcessController@fetchReportHourlyLcq');
// Report Middle Buffing
Route::get('index/middle/report_buffing_ng', 'MiddleProcessController@indexReportBuffingNg');
Route::get('fetch/middle/bff_ng_rate_monthly', 'MiddleProcessController@fetchBuffingNgRateMonthly');
Route::get('fetch/middle/bff_op_eff_monthly', 'MiddleProcessController@fetchBuffingOpEffMonthly');
Route::get('fetch/middle/bff_op_ng_monthly/{id}', 'MiddleProcessController@fetchBuffingOpNgMonthly');
Route::get('fetch/middle/bff_op_ng_monthly_detail', 'MiddleProcessController@fetchBuffingOpNgMonthlyDetail');
Route::get('fetch/middle/bff_op_work_monthly/{id}', 'MiddleProcessController@fetchBuffingOpWorkMonthly');
Route::get('fetch/middle/bff_op_work_monthly_detail', 'MiddleProcessController@fetchBuffingOpWorkMonthlyDetail');
Route::get('fetch/middle/bff_ng_monthly', 'MiddleProcessController@fetchBuffingNgMonthly');
Route::get('fetch/middle/bff_ng_rate_daily', 'MiddleProcessController@fetchBuffingNgDaily');
Route::get('index/middle/report_buffing_operator_time', 'MiddleProcessController@indexReportOpTime');
Route::get('fetch/middle/report_buffing_operator_time', 'MiddleProcessController@fetchReportOpTime');
Route::get('fetch/middle/report_buffing_operator_time_qty', 'MiddleProcessController@fetchReportOpTimeQty');
Route::get('index/middle/report_buffing_canceled_log', 'MiddleProcessController@indexReportBuffingCancelled');
Route::get('fetch/middle/report_buffing_canceled_log', 'MiddleProcessController@fetchReportBuffingCancelled');

//Display Buffing
Route::get('fetch/middle/buffing_hourly_ng', 'MiddleProcessController@fetchBuffingHourlyNg');
Route::get('index/middle/buffing_ng', 'MiddleProcessController@indexBuffingNg');
Route::get('fetch/middle/buffing_ng', 'MiddleProcessController@fetchBuffingNg');
Route::get('fetch/middle/buffing_ng_key', 'MiddleProcessController@fetchBuffingNgKey');
Route::get('index/middle/buffing_op_ng', 'MiddleProcessController@indexBuffingOpNg');
Route::get('fetch/middle/buffing_op_ng', 'MiddleProcessController@fetchBuffingOpNg');
Route::get('fetch/middle/buffing_op_ng_target', 'MiddleProcessController@fetchBuffingOpNgTarget');
Route::get('fetch/middle/buffing_detail_op_ng', 'MiddleProcessController@fetchBuffingDetailOpNg');
Route::get('index/middle/buffing_trend_op_eff', 'MiddleProcessController@indexTrendBuffingOpEff');
Route::get('index/middle/buffing_op_eff', 'MiddleProcessController@indexBuffingOpEff');
Route::get('fetch/middle/buffing_op_eff', 'MiddleProcessController@fetchBuffingOpEff');
Route::get('fetch/middle/buffing_op_eff_detail', 'MiddleProcessController@fetchBuffingOpEffDetail');
Route::get('fetch/middle/buffing_daily_op_eff', 'MiddleProcessController@fetchBuffingDailyOpEff');
Route::get('fetch/middle/buffing_op_working', 'MiddleProcessController@fetchBuffingOpWorking');
Route::get('fetch/middle/buffing_op_result', 'MiddleProcessController@fetchBuffingOpResult');
Route::get('fetch/middle/buffing_op_eff_target', 'MiddleProcessController@fetchBuffingOpEffTarget');
Route::get('index/middle/buffing_op_ranking', 'MiddleProcessController@indexBuffingOpRanking');
Route::get('index/middle/buffing_daily_ng_rate', 'MiddleProcessController@indexBuffingNgRate');
Route::get('fetch/middle/buffing_daily_ng_rate', 'MiddleProcessController@fetchBuffingNgRate');
Route::get('index/middle/buffing_daily_op_ng_rate', 'MiddleProcessController@indexBuffingOpNgRate');
Route::get('fetch/middle/buffing_daily_op_ng_rate', 'MiddleProcessController@fetchBuffingOpNgRate');
Route::get('index/middle/buffing_group_achievement', 'MiddleProcessController@indexBuffingGroupAchievement');
Route::get('fetch/middle/buffing_group_achievement', 'MiddleProcessController@fetchBuffingGroupAchievement');
Route::get('fetch/middle/buffing_accumulated_achievement', 'MiddleProcessController@fetchAccumulatedAchievement');
Route::get('fetch/middle/buffing_daily_group_achievement', 'MiddleProcessController@fetchDailyGroupAchievement');
Route::get('index/middle/buffing_group_balance', 'MiddleProcessController@indexBuffingGroupBalance');
Route::get('fetch/middle/buffing_group_balance', 'MiddleProcessController@fetchBuffingGroupBalance');
Route::get('index/middle/buffing_ic_atokotei', 'MiddleProcessController@indexBuffingIcAtokotei');
Route::get('fetch/middle/buffing_ic_atokotei', 'MiddleProcessController@fetchBuffingIcAtokotei');
Route::get('index/middle/buffing_work_order/{id}', 'MiddleProcessController@indexBuffingWorkOrder');
Route::get('fetch/middle/buffing_target', 'MiddleProcessController@fetchTarget');
Route::get('index/middle/buffing_operator_assesment', 'MiddleProcessController@indexOpAssesment');



//MIZUSUMASHI
Route::get('index/middle/muzusumashi', 'MiddleProcessController@indexMizusumashi');
Route::get('fetch/middle/muzusumashi', 'MiddleProcessController@fetchMisuzumashi');

//WELDING
Route::get('index/process_welding_fl', 'WeldingProcessController@indexWeldingFL');


Route::group(['nav' => 'S20', 'middleware' => 'permission'], function(){
	Route::get('index/qnaHR', 'EmployeeController@indexHRQA');
	Route::get('fetch/hr/hrqa', 'EmployeeController@fetchMasterQuestion');
	Route::get('fetch/hr/hrqa/detail', 'EmployeeController@fetchDetailQuestion');
});

//E - Kaizen
Route::group(['nav' => 'S21', 'middleware' => 'permission'], function(){
	Route::get('index/kaizen', 'EmployeeController@indexKaizen');
	Route::get('fetch/kaizen', 'EmployeeController@fetchDataKaizen');
	Route::get('fetch/kaizen/data', 'EmployeeController@fetchDataKaizenAll');
	Route::get('fetch/kaizen/applied', 'EmployeeController@fetchAppliedKaizen');
	Route::get('index/kaizen/detail/{id}/{ctg}', 'EmployeeController@indexKaizenAssessment');
	Route::post('input/kaizen/detail/note', 'EmployeeController@inputKaizenDetailNote');
	Route::get('index/kaizen/applied', 'EmployeeController@indexKaizenApplied');
	Route::post('assess/kaizen', 'EmployeeController@assessKaizen');
	Route::post('apply/kaizen', 'EmployeeController@applyKaizen');
	Route::get('index/kaizen/data', 'EmployeeController@indexKaizenData');
});

Route::get('index/upload_kaizen', 'EmployeeController@indexUploadKaizenImage');
Route::post('post/upload_kaizen', 'EmployeeController@UploadKaizenImage');
// Route::get('fetch/upload_kaizen/image', 'EmployeeController@fetchEmployee');


Route::get('fetch/kaizen/detail', 'EmployeeController@fetchDetailKaizen');
Route::post('execute/kaizen/excellent', 'EmployeeController@executeKaizenExcellent');

Route::get('index/kaizen/{section}', 'EmployeeController@indexKaizen2');
Route::get('fetch/cost', 'EmployeeController@fetchCost');
Route::get('index/kaizen2/report', 'EmployeeController@indexKaizenReport');
Route::get('fetch/kaizen/report', 'EmployeeController@fetchKaizenReport');
Route::get('index/kaizen2/resume', 'EmployeeController@indexKaizenResume');
Route::get('fetch/kaizen/resume', 'EmployeeController@fetchKaizenResume');
Route::get('fetch/kaizen/resume_detail', 'EmployeeController@fetchKaizenResumeDetail');
Route::get('index/kaizen/aproval/resume', 'EmployeeController@indexKaizenApprovalResume');
Route::get('index/kaizen2/value', 'EmployeeController@indexKaizenReward');
Route::get('fetch/kaizen/value', 'EmployeeController@getKaizenReward');
Route::get('kaizen/session', 'EmployeeController@setSession');

//START CLINIC
Route::group(['nav' => 'S23', 'middleware' => 'permission'], function(){
	Route::get('index/diagnose', 'ClinicController@indexDiagnose');
	Route::get('fetch/diagnose', 'ClinicController@fetchDiagnose');
	Route::post('delete/diagnose', 'ClinicController@deleteVisitor');
	Route::post('input/diagnose', 'ClinicController@inputDiagnose');
	Route::get('index/clinic_visit_log', 'ClinicController@indexVisitLog');
	Route::get('fetch/clinic_visit_log', 'ClinicController@fetchVisitLog');
	Route::get('fetch/clinic_visit_log_excel', 'ClinicController@fetchVisitLogExcel');
	Route::get('fetch/clinic_visit_edit_detail', 'ClinicController@fetchVisitEdit');
	Route::post('edit/diagnose', 'ClinicController@editDiagnose');
	Route::get('fetch/display/clinic_disease_detail', 'ClinicController@fetchDiseaseDetail');
	Route::get('fetch/clinic_visit_detail', 'ClinicController@fetchClinicVisitDetail');
	
	Route::get('index/mask_visit_log', 'ClinicController@indexMaskLog');
	Route::get('fetch/mask_visit_log', 'ClinicController@fetchMaskLog');
	Route::get('fetch/clinic_masker_detail', 'ClinicController@fetchClinicMaskerDetail');


	Route::get('index/medicines', 'ClinicController@indexMedicines');
	Route::get('fetch/medicines', 'ClinicController@fetchMedicines');
	Route::post('edit/medicine_stock', 'ClinicController@editMedicineStock');




});
Route::get('index/display/clinic_monitoring', 'ClinicController@indexClinicMonitoring');
Route::get('index/display/clinic_visit', 'ClinicController@indexClinicVisit');
Route::get('index/display/clinic_disease', 'ClinicController@indexClinicDisease');
Route::get('fetch/display_patient', 'ClinicController@fetchPatient');
Route::get('fetch/daily_clinic_visit', 'ClinicController@fetchDailyClinicVisit');
Route::get('fetch/clinic_visit', 'ClinicController@fetchClinicVisit');
Route::get('fetch/display/clinic_disease', 'ClinicController@fetchDisease');
Route::get('fetch/clinic_masker', 'ClinicController@fetchClinicMasker');



//END CLINIC


//INITIAL
Route::get('index/initial/{id}', 'InitialProcessController@index');
Route::get('index/initial/stock_monitoring/{id}', 'InitialProcessController@indexStockMonitoring');
Route::get('index/initial/stock_trend/{id}', 'InitialProcessController@indexStockTrend');
Route::get('fetch/initial/stock_monitoring', 'InitialProcessController@fetchStockMonitoring');
Route::get('fetch/initial/stock_trend', 'InitialProcessController@fetchStockTrend');
Route::get('fetch/initial/stock_monitoring_detail', 'InitialProcessController@fetchStockMonitoringDetail');
Route::get('fetch/initial/stock_trend_detail', 'InitialProcessController@fetchStockTrendDetail');

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
	Route::get('print/overtime/group', 'OvertimeController@indexPrintHead');
	Route::post('fetch/report/overtime_graph', 'OvertimeController@graphPrint');
	Route::get('fetch/overtime', 'OvertimeController@fetchOvertime');
	Route::get('fetch/overtime/detail', 'OvertimeController@fetchOvertimeDetail');
	Route::get('fetch/overtime/head', 'OvertimeController@fetchOvertimeHead');
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
	//---master code op
	Route::get('index/Op_Code', 'Pianica@opcode');
	Route::get('index/FillOpcode', 'Pianica@fillopcode');
	Route::get('edit/Opcode', 'Pianica@editopcode');
	Route::post('update/Opcode', 'Pianica@updateopcode');


	//-----master op
	Route::get('index/Op', 'Pianica@op');
	Route::get('edit/Op', 'Pianica@editop');
	Route::post('update/Op', 'Pianica@updateop');
	Route::post('add/Op', 'Pianica@addop');


	//----------bensuki

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
	Route::get('fetch/opTunning', 'Pianica@opTunning');



	//-----------kensa akhir
	Route::get('index/KensaAkhir', 'Pianica@kensaakhir');
	Route::post('index/SaveKensaAkhir', 'Pianica@savekensaakhir');

	//------------ kakuning visual
	Route::get('index/KakuningVisual', 'Pianica@kakuningvisual');
	Route::post('index/SaveKakuningVisual', 'Pianica@saveKakuningVisual');
	
	Route::get('index/FillOp', 'Pianica@fillop');
});

Route::get('index/Pianica', 'Pianica@index');
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

	//---------- display Pianica
Route::get('index/display_pn_ng_rate', 'Pianica@indexNgRate');
Route::get('fetch/pianica/ng_spot_welding', 'Pianica@fetchNgWelding');
Route::get('fetch/pianica/ng_bentsuki_benage', 'Pianica@fetchNgBentsukiBenage');
Route::get('fetch/pianica/ng_kensa_awal', 'Pianica@fetchNgKensaAwal');
Route::get('index/display_pn_ng_trends', 'Pianica@indexTrendsNgRate');
Route::get('fetch/pianica/trend_ng_spot_welding', 'Pianica@fetchTrendNgWelding');
Route::get('fetch/pianica/trend_ng_bentsuki_benage', 'Pianica@fetchTrendNgBentsukiBenage');
Route::get('fetch/pianica/trend_ng_kensa_awal', 'Pianica@fetchTrendNgKensaAwal');
Route::get('index/display_daily_pn_ng', 'Pianica@indexDailyNg');
Route::get('fetch/pianica/ng_tuning', 'Pianica@fetchNgTuning');

Route::get('fetch/pianica/totalNgReed', 'Pianica@totalNgReed');
Route::get('fetch/pianica/detailReedTuning', 'Pianica@detailReedTuning');

Route::get('fetch/pianica/totalNgReedSpotWelding', 'Pianica@totalNgReedSpotWelding');






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

//detail chart
Route::get('index/getKensaVisualALL2', 'Pianica@getKensaVisualALL2');
Route::get('index/getKensaBensuki2', 'Pianica@getKensaBensuki2');
Route::get('index/getKensaBensuki3', 'Pianica@getKensaBensuki3');


//detail spot welding
Route::get('index/reportSpotWelding', 'Pianica@reportSpotWelding');
Route::get('fetch/reportSpotWeldingData', 'Pianica@reportSpotWeldingData');
Route::get('fetch/reportSpotWeldingDataDetail', 'Pianica@reportSpotWeldingDataDetail');

//detail spot getReportKensaAwalDaily
Route::get('index/reportKensaAwalDaily', 'Pianica@reportKensaAwalDaily');
Route::get('fetch/getReportKensaAwalDaily', 'Pianica@getReportKensaAwalDaily');

//end pianica

//detail spot getReportKensaAkhirDaily
Route::get('index/reportKensaAkhirDaily', 'Pianica@reportKensaAkhirDaily');
Route::get('fetch/getReportKensaAkhirDaily', 'Pianica@getReportKensaAkhirDaily');

//detail spot getReportVisualDaily
Route::get('index/reportVisualDaily', 'Pianica@reportVisualDaily');
Route::get('fetch/getReportVisualDaily', 'Pianica@getReportVisualDaily');

//end pianica

//stock taking
Route::group(['nav' => 'M23', 'middleware' => 'permission'], function(){
	Route::get('index/bom_output', 'StockTakingController@bom_output');
	Route::get('fetch/bom_output', 'StockTakingController@fetch_bom_output');
});

Route::group(['nav' => 'M24', 'middleware' => 'permission'], function(){
	Route::get('index/material_plant_data_list', 'StockTakingController@mpdl');
	Route::get('fetch/material_plant_data_list', 'StockTakingController@fetchmpdl');
});

Route::group(['nav' => 'S22', 'middleware' => 'permission'], function(){
	Route::get('index/stocktaking/silver/{id}', 'StockTakingController@indexSilver');
	Route::get('fetch/stocktaking/silver_list', 'StockTakingController@fetchSilverList');
	Route::get('fetch/stocktaking/silver_count', 'StockTakingController@fetchSilverCount');
	Route::get('fetch/stocktaking/silver_resume', 'StockTakingController@fetchSilverResume');
	Route::post('input/stocktaking/silver_count', 'StockTakingController@inputSilverCount');
	Route::post('input/stocktaking/silver_final', 'StockTakingController@inputSilverFinal');
});
Route::get('index/stocktaking/silver_report', 'StockTakingController@indexSilverReport');
Route::get('fetch/stocktaking/silver_report', 'StockTakingController@fetchSilverReport');
Route::get('fetch/stocktaking/silver_report_modal', 'StockTakingController@fetchSilverReportModal');



//Index Monthly
Route::get('index/stocktaking/menu', 'StockTakingController@indexMonthlyStocktaking');

Route::get('fetch/stocktaking/filled_list', 'StockTakingController@fetchfilledList');
Route::get('fetch/stocktaking/filled_list_detail', 'StockTakingController@fetchfilledListDetail');
Route::get('fetch/stocktaking/variance', 'StockTakingController@fetchVariance');
Route::get('fetch/stocktaking/variance_detail', 'StockTakingController@fetchVarianceDetail');

Route::get('export/stocktaking/inquiry', 'StockTakingController@exportInquiry');
Route::get('export/stocktaking/variance', 'StockTakingController@exportVariance');

//Summary of Counting
Route::get('index/stocktaking/summary_of_counting', 'StockTakingController@indexSummaryOfCounting');
Route::get('fetch/stocktaking/summary_of_counting', 'StockTakingController@fetchSummaryOfCounting');
Route::get('print/stocktaking/summary_of_counting', 'StockTakingController@printSummaryOfCounting');

//No Use
Route::get('index/stocktaking/no_use', 'StockTakingController@indexNoUse');
Route::post('fetch/stocktaking/update_no_use', 'StockTakingController@updateNoUse');

//Count
Route::get('index/stocktaking/count', 'StockTakingController@indexCount');
Route::get('fetch/stocktaking/material_detail', 'StockTakingController@fetchMaterialDetail');
Route::get('fetch/stocktaking/store_list', 'StockTakingController@fetchStoreList');
Route::post('fetch/stocktaking/update_count', 'StockTakingController@updateCount');

//Audit
Route::get('index/stocktaking/audit/{id}', 'StockTakingController@indexAudit');
Route::get('fetch/stocktaking/audit_store_list', 'StockTakingController@fetchAuditStoreList');
Route::get('fetch/stocktaking/check_confirm/{id}', 'StockTakingController@fetchCheckAudit');
Route::post('fetch/stocktaking/update_audit/{id}', 'StockTakingController@updateAudit');
Route::post('fetch/stocktaking/update_process/{id}', 'StockTakingController@updateProcessAudit');

//Count PI
Route::get('index/stocktaking/unmatch', 'StockTakingController@indexUnmatch');

//Unmatch
Route::get('index/stocktaking/count_pi', 'StockTakingController@indexCountPI');

//Revise
Route::get('index/stocktaking/revise', 'StockTakingController@indexRevise');
Route::get('fetch/stocktaking/revise', 'StockTakingController@fetchRevise');
Route::post('fetch/stocktaking/update_revise', 'StockTakingController@updateRevise');






Route::group(['nav' => 'S28', 'middleware' => 'permission'], function(){
	//Pesanan + master
	Route::get('index/pantry/pesanmenu', 'PantryController@pesanmenu');
	Route::get('index/pantry/menu', 'PantryController@daftarmenu');
	Route::get('index/pantry/pesanan', 'PantryController@daftarpesanan');
	Route::get('index/pantry/confirmation', 'PantryController@daftarkonfirmasi');

	//Pesanan
	Route::get('fetch/menu', 'PantryController@fetchmenu');
	Route::get('fetch/pesanan', 'PantryController@fetchpesanan');
	Route::post('fetch/pantry/pesanan','PantryController@filter');
	Route::get('fetch/konfirmasi/pesanan','PantryController@filterkonfirmasi');

	Route::post('index/pantry/inputmenu', 'PantryController@inputMenu');
	Route::post('index/pantry/deletemenu', 'PantryController@deleteMenu');
	Route::post('index/pantry/konfirmasipesanan', 'PantryController@konfirmasipesanan');

	//CRUD Menu
	Route::get('index/pantry/create_menu', 'PantryController@create_menu');
	Route::post('index/pantry/create_menu', 'PantryController@create_menu_action');
	Route::get('index/pantry/delete_menu/{id}', 'PantryController@delete_menu');
	Route::get('index/pantry/edit_menu/{id}', 'PantryController@edit_menu');
	Route::post('index/pantry/edit_menu/{id}', 'PantryController@edit_menu_action');

	//Konfirmasi Pesanan
	Route::post('index/pantry/konfirmasi', 'PantryController@konfirmasi');
	Route::post('index/pantry/selesaikan', 'PantryController@selesaikan');

	Route::get('fetch/pantry/pesan','PantryController@getPesanan');

	Route::get('index/display/pantry', 'PantryController@konfirmasiasd');
	
	Route::get('fetch/pantry/visitor_detail', 'PantryController@fetchPantryVisitorDetail');
});
Route::get('index/display/pantry_visit', 'PantryController@indexDisplayPantryVisit');
Route::get('fetch/pantry/realtime_visitor', 'PantryController@fetchPantryRealtimeVisitor');
Route::get('fetch/pantry/visitor', 'PantryController@fetchPantryVisitor');

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
	Route::get('fill/reason', 'CheckSheet@getReason');

	Route::get('delete/deleteReimport', 'CheckSheet@deleteReimport');

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

// return sax
Route::get('index/repairSx', 'ProcessController@indexRepairSx');
Route::get('fetch/returnTableSx', 'ProcessController@fetchReturnTableSx');
// end return sax

// ng sax
Route::get('index/ngSx', 'ProcessController@indexngSx');
Route::get('fetch/ngTableSx', 'ProcessController@fetchngTableSx');
// end ng sax

// ng sax
Route::get('index/ngFL', 'ProcessController@indexngFL');
Route::get('fetch/ngTableFL', 'ProcessController@fetchngTableFL');
// end ng sax


// return cl
Route::get('index/repairCl', 'ProcessController@indexRepairCl');
Route::get('fetch/returnTableCl', 'ProcessController@fetchReturnTableCl');
// end return cl

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
Route::get('index/getdatareprintAll2', 'ProcessController@getModelReprintAll2');



Route::get('index/process_stamp_cl', 'ProcessController@indexProcessStampCl');
Route::get('index/process_stamp_sx', 'ProcessController@indexProcessStampSX');
Route::get('index/process_stamp_sx_assy', 'ProcessController@indexProcessStampSXassy');
Route::get('stamp/resumes_cl', 'ProcessController@indexResumesCl');
Route::post('stamp/stamp_detail_cl', 'ProcessController@filter_stamp_detail_cl');
Route::get('stamp/resumes_sx', 'ProcessController@indexResumesSX');
Route::post('stamp/stamp_detail_sx', 'ProcessController@filter_stamp_detail_sx');
Route::get('fetch/fetch_plan_labelsax/{id}', 'ProcessController@fetch_plan_labelsax');

//end tambah ali


// label flute

Route::get('index/label_fl', 'ProcessController@indexLabelFL');
Route::get('stamp/fetchResultFL5', 'ProcessController@fetchResultFL5');
Route::get('stamp/fetchStampPlanFL5', 'ProcessController@fetchStampPlanFL5');
Route::get('index/getModelfl', 'ProcessController@getModelfl');
Route::get('index/get_snfl', 'ProcessController@getsnsaxfl');

Route::post('index/print_FL', 'ProcessController@print_FL');
Route::get('edit/stampLabelFL', 'ProcessController@editStampLabelFL');
Route::post('update/stampLabelFL', 'ProcessController@updateStampLabelFL');
Route::get('index/getModelReprintAllFL', 'ProcessController@getModelReprintAllFL');

Route::get('index/fl_label_outer/{id}/{gmc}/{remark}', 'ProcessController@label_besar_outer_fl');
Route::get('index/fl_label_besar/{id}/{gmc}/{remark}', 'ProcessController@label_besar_fl');
Route::get('index/fl_label_kecil/{id}/{remark}', 'ProcessController@label_kecil_fl');
Route::get('index/fl_label_kecil2/{id}/{remark}', 'ProcessController@label_kecil2_fl');


Route::get('index/label_des_fl/{id}', 'ProcessController@label_des_fl');


//end label flute

// check sheet sax

Route::get('index/process_stamp_sx_4/{model}/{sn}', 'ProcessController@indexProcessAssyFLSaxT4');
Route::get('fetch/image_sax', 'ProcessController@fetchImageSax');

Route::get('index/process_stamp_sx_check', 'ProcessController@indexProcessAssyFLSaxTCheck');


// end check sheet sax

// new sax result

Route::get('index/fetchResultSaxnew', 'ProcessController@indexfetchResultSaxnew');
Route::get('fetch/fetchResultSaxnew', 'ProcessController@fetchResultSaxnew');
//end new sax result 

// new fl result
Route::get('index/fetchResultFlnew', 'ProcessController@indexfetchResultFlStamp');
Route::get('fetch/fetchResultFlnew', 'ProcessController@fetchResultFlStamp');
//end new fl result 

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
Route::post('scan/educational_instrument', 'FloController@scan_educational_instrument');
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


//SHIPMENT REPORT
Route::get('index/display/shipment_report', 'DisplayController@indexShipmentReport');
Route::get('fetch/display/shipment_report', 'DisplayController@fetchShipmentReport');
Route::get('fetch/display/shipment_report_detail', 'DisplayController@fetchShipmentReportDetail');


//DISPLAY EXPORT PROGRESS
Route::get('fetch/display/shipment_progress', 'DisplayController@fetchShipmentProgress');
Route::get('fetch/display/modal_shipment_progress', 'DisplayController@fetchModalShipmentProgress');
Route::get('index/display/shipment_progress', 'DisplayController@indexShipmentProgress');


//DISPLAY STUFFING PROGRESS
Route::get('index/display/stuffing_progress', 'DisplayController@indexStuffingProgress');
Route::get('fetch/display/stuffing_progress', 'DisplayController@fetchStuffingProgress');
Route::get('fetch/display/stuffing_detail', 'DisplayController@fetchStuffingDetail');
Route::get('index/display/all_stock', 'DisplayController@indexAllStock');
Route::get('fetch/display/all_stock', 'DisplayController@fetchAllStock');


//DISPLAY STUFFING TIME
Route::get('index/display/stuffing_time', 'DisplayController@indexStuffingTime');

//DISPLAY STUFFING MONITORING
Route::get('index/display/stuffing_monitoring', 'DisplayController@indexStuffingMonitoring');

//ASSY PICKING
Route::get('index/display/sub_assy/{id}', 'AssyProcessController@indexDisplayAssy');
Route::get('fetch/display/sub_assy/{id}', 'AssyProcessController@fetchPicking');
Route::get('fetch/display/welding/{id}', 'AssyProcessController@fetchPickingWelding');

Route::get('fetch/chart/sub_assy', 'AssyProcessController@chartPicking');
Route::get('fetch/detail/sub_assy', 'AssyProcessController@fetchPickingDetail');


//Production Report
Route::get('index/production_report/index/{id}', 'ProductionReportController@index');
Route::get('index/production_report/activity/{id}', 'ProductionReportController@activity');
Route::get('index/production_report/report_all/{id}', 'ProductionReportController@report_all');
Route::get('index/production_report/report_by_task/{id}', 'ProductionReportController@report_by_task');
Route::get('index/production_report/fetchReportByTask/{id}', 'ProductionReportController@fetchReportByTask');
Route::get('index/production_report/fetchReport/{id}', 'ProductionReportController@fetchReport');
Route::get('index/production_report/fetchReportDaily/{id}', 'ProductionReportController@fetchReportDaily');
Route::get('index/production_report/fetchReportWeekly/{id}', 'ProductionReportController@fetchReportWeekly');
Route::get('index/production_report/fetchReportMonthly/{id}', 'ProductionReportController@fetchReportMonthly');
Route::get('index/production_report/fetchReportDetailMonthly/{id}', 'ProductionReportController@fetchReportDetailMonthly');
Route::get('index/production_report/fetchReportDetailConditional/{id}', 'ProductionReportController@fetchReportDetailConditional');
Route::get('index/production_report/fetchReportDetailWeekly/{id}', 'ProductionReportController@fetchReportDetailWeekly');
Route::get('index/production_report/fetchReportDetailDaily/{id}', 'ProductionReportController@fetchReportDetailDaily');
Route::get('index/production_report/fetchReportConditional/{id}', 'ProductionReportController@fetchReportConditional');
Route::get('index/production_report/fetchReportAudit/{id}', 'ProductionReportController@fetchReportAudit');
Route::get('index/production_report/fetchReportTraining/{id}', 'ProductionReportController@fetchReportTraining');
Route::get('index/production_report/fetchReportSampling/{id}', 'ProductionReportController@fetchReportSampling');
Route::get('index/production_report/fetchReportLaporanAktivitas/{id}', 'ProductionReportController@fetchReportLaporanAktivitas');
Route::get('index/production_report/fetchPlanReport/{id}', 'ProductionReportController@fetchPlanReport');
Route::get('fetch/production_report/detail_stat/{id}', 'ProductionReportController@detailProductionReport');
Route::get('fetch/production_report/detail_training/{id}', 'ProductionReportController@detailTraining');
Route::get('fetch/production_report/detail_sampling_check/{id}', 'ProductionReportController@detailSamplingCheck');
Route::get('index/production_report/report_by_act_type/{id}/{activity_type}', 'ProductionReportController@report_by_act_type');
Route::get('index/production_report/fetchReportByLeader/{id}', 'ProductionReportController@fetchReportByLeader');
Route::get('index/production_report/fetchDetailReportWeekly/{id}', 'ProductionReportController@fetchDetailReportWeekly');
Route::get('index/production_report/fetchPointCheck/{id}', 'ProductionReportController@fetchPointCheck');
Route::get('index/production_report/fetchDetailReportPrev/{id}', 'ProductionReportController@fetchDetailReportPrev');
Route::get('index/production_report/fetchDetailReportMonthly/{id}', 'ProductionReportController@fetchDetailReportMonthly');
Route::get('index/production_report/fetchDetailReportDaily/{id}', 'ProductionReportController@fetchDetailReportDaily');

//APPROVAL LEADER TASK
Route::get('index/production_report/approval/{id}', 'ProductionReportController@approval');
Route::get('index/production_report/approval_list/{id}/{leader_name}', 'ProductionReportController@approval_list');
Route::post('index/production_report/approval_list_filter/{id}/{leader_name}', 'ProductionReportController@approval_list_filter');
Route::get('index/production_report/approval_detail/{activity_list_id}/{month}', 'ProductionReportController@approval_detail');


//Activity List
Route::get('index/activity_list', 'ActivityListController@index');
Route::get('index/activity_list/resume/{id}', 'ActivityListController@resume');
Route::post('index/activity_list/resume_filter/{id}', 'ActivityListController@resume_filter');
Route::get('index/activity_list/create', 'ActivityListController@create');
Route::get('index/activity_list/create_by_department/{id}/{no}', 'ActivityListController@create_by_department');
Route::post('index/activity_list/store', 'ActivityListController@store');
Route::post('index/activity_list/store_by_department/{id}/{no}', 'ActivityListController@store_by_department');
Route::get('index/activity_list/show/{id}', 'ActivityListController@show');
Route::get('index/activity_list/destroy/{id}', 'ActivityListController@destroy');
Route::get('index/activity_list/destroy_by_department/{id}/{department_id}/{no}', 'ActivityListController@destroy_by_department');
Route::get('index/activity_list/edit/{id}', 'ActivityListController@edit');
Route::get('index/activity_list/edit_by_department/{id}/{department_id}/{no}', 'ActivityListController@edit_by_department');
Route::post('index/activity_list/update/{id}', 'ActivityListController@update');
Route::post('index/activity_list/update_by_department/{id}/{department_id}/{no}', 'ActivityListController@update_by_department');
Route::get('index/activity_list/filter/{id}/{no}/{frequency}', 'ActivityListController@filter');

//production audit
Route::get('index/production_audit/index/{id}/{product}/{proses}', 'ProductionAuditController@index');
Route::get('index/production_audit/details/{id}', 'ProductionAuditController@details');
Route::get('index/production_audit/show/{id}/{audit_id}', 'ProductionAuditController@show');
Route::get('index/production_audit/destroy/{id}/{audit_id}/{product}/{proses}', 'ProductionAuditController@destroy');
Route::post('index/production_audit/filter_audit/{id}/{product}/{proses}', 'ProductionAuditController@filter_audit');
Route::get('index/production_audit/create/{id}/{product}/{proses}', 'ProductionAuditController@create');
Route::get('index/production_audit/create_by_point_check/{id}/{product}/{proses}/{point_check_id}', 'ProductionAuditController@create_by_point_check');
Route::post('index/production_audit/store/{id}/{product}/{proses}', 'ProductionAuditController@store');
Route::get('index/production_audit/edit/{id}/{audit_id}/{product}/{proses}', 'ProductionAuditController@edit');
Route::post('index/production_audit/update/{id}/{audit_id}/{product}/{proses}', 'ProductionAuditController@update');
Route::get('cities/get_by_country', 'ProductionAuditController@get_by_country')->name('admin.cities.get_by_country');
Route::post('index/production_audit/print_audit/{id}', 'ProductionAuditController@print_audit');
Route::get('index/production_audit/print_audit_email/{id}/{date}/{product}/{proses}', 'ProductionAuditController@print_audit_email');
Route::get('index/production_audit/print_audit_chart/{id}/{date}/{product}/{proses}', 'ProductionAuditController@print_audit_chart');
Route::get('index/production_audit/report_audit/{id}', 'ProductionAuditController@report_audit');
Route::get('index/production_audit/fetchReport/{id}', 'ProductionAuditController@fetchReport');
Route::get('fetch/production_audit/detail_stat/{id}', 'ProductionAuditController@detailProductionAudit');
Route::get('index/production_audit/signature', 'ProductionAuditController@signature');
Route::post('index/production_audit/save_signature', 'ProductionAuditController@save_signature');
Route::post('index/production_audit/sendemail/{id}', 'ProductionAuditController@sendemail');
Route::post('index/production_audit/approval/{id}', 'ProductionAuditController@approval');

//point check master
Route::get('index/point_check_audit/index/{id}', 'PointCheckController@index');
Route::post('index/point_check_audit/filter_point_check/{id}', 'PointCheckController@filter_point_check');
Route::get('index/point_check_audit/show/{id}/{point_check_audit_id}', 'PointCheckController@show');
Route::get('index/point_check_audit/show2/{point_check_audit_id}', 'PointCheckController@show2');
Route::get('index/point_check_audit/destroy/{id}/{point_check_audit_id}', 'PointCheckController@destroy');
Route::get('index/point_check_audit/create/{id}', 'PointCheckController@create');
Route::post('index/point_check_audit/store/{id}', 'PointCheckController@store');
Route::get('index/point_check_audit/edit/{id}/{point_check_audit_id}', 'PointCheckController@edit');
Route::post('index/point_check_audit/update/{id}/{point_check_audit_id}', 'PointCheckController@update');

//training
Route::get('index/training_report/index/{id}', 'TrainingReportController@index');
Route::post('index/training_report/filter_training/{id}', 'TrainingReportController@filter_training');
Route::get('index/training_report/show/{id}/{training_id}', 'TrainingReportController@show');
Route::get('index/training_report/create/{id}', 'TrainingReportController@create');
Route::post('index/training_report/store/{id}', 'TrainingReportController@store');
Route::get('index/training_report/destroy/{id}/{training_id}', 'TrainingReportController@destroy');
Route::get('index/training_report/edit/{id}/{training_id}', 'TrainingReportController@edit');
Route::post('index/training_report/update/{id}/{training_id}', 'TrainingReportController@update');
Route::get('index/training_report/details/{id}/{session_training}', 'TrainingReportController@details');
Route::post('index/training_report/insertpicture/{id}', 'TrainingReportController@insertpicture');
Route::post('index/training_report/insertparticipant/{id}', 'TrainingReportController@insertparticipant');
Route::get('index/training_report/destroypicture/{id}/{picture_id}', 'TrainingReportController@destroypicture');
Route::get('index/training_report/destroyparticipant/{id}/{participant_id}', 'TrainingReportController@destroyparticipant');
Route::post('index/training_report/editpicture/{id}/{picture_id}', 'TrainingReportController@editpicture');
Route::post('index/training_report/editparticipant/{id}/{participant_id}', 'TrainingReportController@editparticipant');
Route::get('index/training_report/report_training/{id}', 'TrainingReportController@report_training');
Route::get('index/training_report/fetchReport/{id}', 'TrainingReportController@fetchReport');
Route::get('fetch/training_report/detail_stat/{id}', 'TrainingReportController@detailTraining');
Route::get('index/training_report/print/{id}', 'TrainingReportController@print_training');
Route::get('index/training_report/print_training_email/{id}', 'TrainingReportController@print_training_email');
Route::get('index/training_report/print_training_approval/{id}/{month}', 'TrainingReportController@print_training_approval');
Route::get('index/training_report/scan_employee/{id}', 'TrainingReportController@scan_employee');
Route::get('index/training_report/cek_employee/{nik}/{id}','TrainingReportController@cek_employee');
Route::post('index/training_report/cek_employee2/{nik}/{id}','TrainingReportController@cek_employee2');
Route::get('index/training_participant/edit','TrainingReportController@getparticipant')->name('admin.participantedit');
Route::get('index/training_report/sendemail/{id}', 'TrainingReportController@sendemail');
Route::post('index/training_report/approval/{id}', 'TrainingReportController@approval');
Route::post('index/training_report/importparticipant/{id}', 'TrainingReportController@importparticipant');

//sampling check
Route::get('index/sampling_check/index/{id}', 'SamplingCheckController@index');
Route::post('index/sampling_check/filter_sampling/{id}', 'SamplingCheckController@filter_sampling');
Route::get('index/sampling_check/create/{id}', 'SamplingCheckController@create');
Route::post('index/sampling_check/store/{id}', 'SamplingCheckController@store');
Route::get('index/sampling_check/show/{id}/{sampling_check_id}', 'SamplingCheckController@show');
Route::get('index/sampling_check/destroy/{id}/{sampling_check_id}', 'SamplingCheckController@destroy');
Route::get('index/sampling_check/edit/{id}/{sampling_check_id}', 'SamplingCheckController@edit');
Route::post('index/sampling_check/update/{id}/{sampling_check_id}', 'SamplingCheckController@update');
Route::get('index/sampling_check/details/{sampling_check_id}', 'SamplingCheckController@details');
Route::get('index/sampling_check/createdetails/{sampling_check_id}', 'SamplingCheckController@createdetails');
Route::post('index/sampling_check/storedetails/{sampling_check_id}', 'SamplingCheckController@storedetails');
Route::get('index/sampling_check/destroydetails/{sampling_id}/{sampling_check_id}', 'SamplingCheckController@destroydetails');
Route::get('index/sampling_check/createdetails/{sampling_check_id}', 'SamplingCheckController@createdetails');
Route::post('index/sampling_check/storedetails/{sampling_check_id}', 'SamplingCheckController@storedetails');
Route::get('index/sampling_check/editdetails/{id}/{sampling_check_details_id}', 'SamplingCheckController@editdetails');
Route::post('index/sampling_check/updatedetails/{id}/{sampling_check_details_id}', 'SamplingCheckController@updatedetails');
Route::get('index/sampling_check/report_sampling_check/{id}', 'SamplingCheckController@report_sampling_check');
Route::get('index/sampling_check/fetchReport/{id}', 'SamplingCheckController@fetchReport');
Route::get('fetch/sampling_check/detail_stat/{id}', 'SamplingCheckController@detail_sampling_check');
Route::post('index/sampling_check/print_sampling/{id}', 'SamplingCheckController@print_sampling');
Route::get('index/sampling_check/print_sampling_email/{id}/{month}', 'SamplingCheckController@print_sampling_email');
Route::get('index/sampling_check/print_sampling_chart/{id}/{subsection}/{month}', 'SamplingCheckController@print_sampling_chart');
Route::post('index/sampling_check/approval/{id}/{month}', 'SamplingCheckController@approval');
Route::post('index/sampling_check/send_email/{id}', 'SamplingCheckController@sendemail');

//Laporan AKtivitas Audit
Route::get('index/audit_report_activity/index/{id}', 'AuditReportActivityController@index');
Route::post('index/audit_report_activity/filter_audit_report/{id}', 'AuditReportActivityController@filter_audit_report');
Route::get('index/audit_report_activity/create/{id}', 'AuditReportActivityController@create');
Route::post('index/audit_report_activity/store/{id}', 'AuditReportActivityController@store');
Route::get('index/audit_report_activity/show/{id}/{audit_report_id}', 'AuditReportActivityController@show');
Route::get('index/audit_report_activity/destroy/{id}/{audit_report_id}', 'AuditReportActivityController@destroy');
Route::get('index/audit_report_activity/edit/{id}/{audit_report_id}', 'AuditReportActivityController@edit');
Route::post('index/audit_report_activity/update/{id}/{audit_report_id}', 'AuditReportActivityController@update');
Route::get('index/audit_report_activity/report_audit_activity/{id}', 'AuditReportActivityController@report_audit_activity');
Route::get('index/audit_report_activity/fetchReport/{id}', 'AuditReportActivityController@fetchReport');
Route::get('fetch/audit_report_activity/detail_laporan_aktivitas/{id}', 'AuditReportActivityController@detail_laporan_aktivitas');
Route::post('index/audit_report_activity/print_audit_report/{id}', 'AuditReportActivityController@print_audit_report');
Route::get('index/audit_report_activity/print_audit_report_chart/{id}/{subsection}/{month}', 'AuditReportActivityController@print_audit_report_chart');
Route::get('index/audit_report_activity/print_audit_report_email/{id}/{month}', 'AuditReportActivityController@print_audit_report_email');
Route::post('index/audit_report_activity/send_email/{id}', 'AuditReportActivityController@sendemail');
Route::post('index/audit_report_activity/approval/{id}', 'AuditReportActivityController@approval');
Route::get('index/getemployee', 'AuditReportActivityController@getemployee');

//Interview
Route::get('index/interview/index/{id}', 'InterviewController@index');
Route::post('index/interview/filter_interview/{id}', 'InterviewController@filter_interview');
Route::get('index/interview/show/{id}/{interview_id}', 'InterviewController@show');
Route::get('index/interview/destroy/{id}/{interview_id}', 'InterviewController@destroy');
Route::get('index/interview/create/{id}', 'InterviewController@create');
Route::post('index/interview/store/{id}', 'InterviewController@store');
Route::get('index/interview/edit/{id}/{interview_id}', 'InterviewController@edit');
Route::post('index/interview/update/{id}/{interview_id}', 'InterviewController@update');
Route::get('index/interview/details/{interview_id}', 'InterviewController@details');
Route::post('index/interview/create_participant', 'InterviewController@create_participant');
Route::get('index/interview/getdetail','InterviewController@getdetail')->name('interview.getdetail');
Route::post('index/interview/edit_participant/{interview_id}/{detail_id}', 'InterviewController@edit_participant');
Route::get('index/interview/destroy_participant/{interview_id}/{detail_id}', 'InterviewController@destroy_participant');
Route::get('index/interview/print_interview/{interview_id}', 'InterviewController@print_interview');
Route::get('index/interview/print_email/{interview_id}', 'InterviewController@print_email');
Route::get('index/interview/print_approval/{activity_list_id}/{month}', 'InterviewController@print_approval');
Route::post('index/interview/approval/{interview_id}', 'InterviewController@approval');
Route::get('index/interview/sendemail/{interview_id}', 'InterviewController@sendemail');
Route::post('index/interview/insertpicture/{id}', 'InterviewController@insertpicture');
Route::get('index/interview/destroypicture/{id}/{picture_id}', 'InterviewController@destroypicture');
Route::post('index/interview/editpicture/{id}/{picture_id}', 'InterviewController@editpicture');

//DAILY CHECK FG
Route::get('index/daily_check_fg/product/{id}', 'DailyCheckController@product');
Route::get('index/daily_check_fg/index/{id}/{product}', 'DailyCheckController@index');
Route::post('index/daily_check_fg/filter_daily_check/{id}/{product}', 'DailyCheckController@filter_daily_check');
Route::get('index/daily_check_fg/show/{id}/{daily_check_id}', 'DailyCheckController@show');
Route::get('index/daily_check_fg/destroy/{id}/{daily_check_id}', 'DailyCheckController@destroy');
Route::get('index/daily_check_fg/create/{id}/{product}', 'DailyCheckController@create');
Route::post('index/daily_check_fg/store/{id}/{product}', 'DailyCheckController@store');
Route::get('index/daily_check_fg/getdetail','DailyCheckController@getdetail')->name('daily_check_fg.getdetail');
Route::post('index/daily_check_fg/update/{id}/{product}', 'DailyCheckController@update');
Route::post('index/daily_check_fg/print_daily_check/{id}', 'DailyCheckController@print_daily_check');
Route::post('index/daily_check_fg/sendemail/{id}', 'DailyCheckController@sendemail');
Route::get('index/daily_check_fg/print_daily_check_email/{id}/{month}', 'DailyCheckController@print_daily_check_email');
Route::post('index/daily_check_fg/approval/{id}/{month}', 'DailyCheckController@approval');

//LABELING
Route::get('index/labeling/index/{id}', 'LabelingController@index');
Route::post('index/labeling/filter_labeling/{id}', 'LabelingController@filter_labeling');
Route::get('index/labeling/show/{id}/{labeling_id}', 'LabelingController@show');
Route::get('index/labeling/destroy/{id}/{labeling_id}', 'LabelingController@destroy');
Route::get('index/labeling/create/{id}', 'LabelingController@create');
Route::post('index/labeling/store/{id}', 'LabelingController@store');
Route::get('index/labeling/edit/{id}/{labeling_id}', 'LabelingController@edit');
Route::post('index/labeling/update/{id}/{labeling_id}', 'LabelingController@update');
Route::post('index/labeling/print_labeling/{id}', 'LabelingController@print_labeling');
Route::get('index/labeling/print_labeling_email/{id}/{month}', 'LabelingController@print_labeling_email');
Route::post('index/labeling/sendemail/{id}', 'LabelingController@sendemail');
Route::post('index/labeling/approval/{id}/{month}', 'LabelingController@approval');

//AUDIT PROCESS
Route::get('index/audit_process/index/{id}', 'AuditProcessController@index');
Route::post('index/audit_process/filter_audit_process/{id}', 'AuditProcessController@filter_audit_process');
Route::get('index/audit_process/show/{id}/{audit_process_id}', 'AuditProcessController@show');
Route::get('index/audit_process/destroy/{id}/{audit_process_id}', 'AuditProcessController@destroy');
Route::get('index/audit_process/create/{id}', 'AuditProcessController@create');
Route::post('index/audit_process/store/{id}', 'AuditProcessController@store');
Route::get('index/audit_process/edit/{id}/{audit_process_id}', 'AuditProcessController@edit');
Route::post('index/audit_process/update/{id}/{audit_process_id}', 'AuditProcessController@update');
Route::post('index/audit_process/print_audit_process/{id}', 'AuditProcessController@print_audit_process');
Route::post('index/audit_process/sendemail/{id}', 'AuditProcessController@sendemail');
Route::get('index/audit_process/print_audit_process_email/{id}/{month}', 'AuditProcessController@print_audit_process_email');
Route::post('index/audit_process/approval/{id}/{month}', 'AuditProcessController@approval');

//FIRST PRODUCT AUDIT
Route::get('index/first_product_audit/index/{id}', 'FirstProductAuditController@index');
Route::get('index/first_product_audit/list_proses/{id}', 'FirstProductAuditController@list_proses');
Route::get('index/first_product_audit/show/{id}/{first_product_audit_id}', 'FirstProductAuditController@show');
Route::get('index/first_product_audit/destroy/{id}/{first_product_audit_id}', 'FirstProductAuditController@destroy');
Route::get('index/first_product_audit/create/{id}', 'FirstProductAuditController@create');
Route::post('index/first_product_audit/store/{id}', 'FirstProductAuditController@store');
Route::get('index/first_product_audit/edit/{id}/{first_product_audit_id}', 'FirstProductAuditController@edit');
Route::post('index/first_product_audit/update/{id}/{first_product_audit_id}', 'FirstProductAuditController@update');
Route::get('index/first_product_audit/details/{id}/{first_product_audit_id}', 'FirstProductAuditController@details');
Route::post('index/first_product_audit/filter_first_product_detail/{id}/{first_product_audit_id}', 'FirstProductAuditController@filter_first_product_detail');
Route::post('index/first_product_audit/store_details/{id}/{first_product_audit_id}', 'FirstProductAuditController@store_details');
Route::get('index/first_product_audit/getdetail','FirstProductAuditController@getdetail')->name('first_product_audit.getdetail');
Route::post('index/first_product_audit/update_details/{id}/{first_product_audit_detail_id}','FirstProductAuditController@update_details');
Route::get('index/first_product_audit/destroy_details/{id}/{first_product_audit_detail_id}','FirstProductAuditController@destroy_details');
Route::post('index/first_product_audit/print_first_product_audit/{id}/{first_product_audit_id}','FirstProductAuditController@print_first_product_audit');
Route::get('index/first_product_audit/print_first_product_audit_email/{id}/{first_product_audit_id}/{month}','FirstProductAuditController@print_first_product_audit_email');
Route::post('index/first_product_audit/sendemail/{id}/{first_product_audit_id}','FirstProductAuditController@sendemail');
Route::post('index/first_product_audit/approval/{id}/{first_product_audit_id}/{month}','FirstProductAuditController@approval');

//daily first product audit
Route::get('index/first_product_audit/daily/{id}/{first_product_audit_id}', 'FirstProductAuditController@daily');
Route::post('index/first_product_audit/filter_first_product_daily/{id}/{first_product_audit_id}', 'FirstProductAuditController@filter_first_product_daily');
Route::post('index/first_product_audit/store_daily/{id}/{first_product_audit_id}', 'FirstProductAuditController@store_daily');
Route::get('index/first_product_audit/getdaily','FirstProductAuditController@getdaily')->name('first_product_audit.getdaily');
Route::post('index/first_product_audit/update_daily/{id}/{first_product_audit_detail_id}','FirstProductAuditController@update_daily');
Route::get('index/first_product_audit/destroy_daily/{id}/{first_product_audit_detail_id}','FirstProductAuditController@destroy_daily');
Route::post('index/first_product_audit/print_first_product_audit_daily/{id}/{first_product_audit_id}','FirstProductAuditController@print_first_product_audit_daily');
Route::get('index/first_product_audit/print_first_product_audit_email_daily/{id}/{first_product_audit_id}/{month}','FirstProductAuditController@print_first_product_audit_email_daily');
Route::post('index/first_product_audit/sendemail_daily/{id}/{first_product_audit_id}','FirstProductAuditController@sendemail_daily');
Route::post('index/first_product_audit/approval_daily/{id}/{first_product_audit_id}/{month}','FirstProductAuditController@approval_daily');

//SCHEDULE AUDIT IK
Route::get('index/audit_guidance/index/{id}', 'AuditGuidanceController@index');
Route::post('index/audit_guidance/filter_guidance/{id}', 'AuditGuidanceController@filter_guidance');
Route::get('index/audit_guidance/show/{id}/{audit_guidance_id}', 'AuditGuidanceController@show');
Route::get('index/audit_guidance/destroy/{id}/{audit_guidance_id}', 'AuditGuidanceController@destroy');
Route::post('index/audit_guidance/store/{id}', 'AuditGuidanceController@store');
Route::get('index/audit_guidance/getdetail','AuditGuidanceController@getdetail')->name('audit_guidance.getdetail');
Route::post('index/audit_guidance/update/{id}/{audit_guidance_id}', 'AuditGuidanceController@update');

//report leader tasks
Route::group(['nav' => 'M25', 'middleware' => 'permission'], function(){
	Route::get('index/leader_task_report/index/{id}', 'LeaderTaskReportController@index');
	Route::get('index/leader_task_report/leader_task_list/{id}/{leader_name}', 'LeaderTaskReportController@leader_task_list');
	Route::post('index/leader_task_report/filter_leader_task/{id}/{leader_name}', 'LeaderTaskReportController@filter_leader_task');
	Route::get('index/leader_task_report/leader_task_detail/{activity_list_id}/{month}', 'LeaderTaskReportController@leader_task_detail');
});


//AREA CHECK POINT
Route::get('index/area_check_point/index/{id}', 'AreaCheckPointController@index');
Route::get('index/area_check_point/show/{id}/{area_check_point_id}', 'AreaCheckPointController@show');
Route::get('index/area_check_point/destroy/{id}/{area_check_point_id}', 'AreaCheckPointController@destroy');
Route::post('index/area_check_point/store/{id}', 'AreaCheckPointController@store');
Route::get('index/area_check_point/getdetail','AreaCheckPointController@getdetail')->name('area_check_point.getdetail');
Route::post('index/area_check_point/update/{id}/{area_check_point_id}', 'AreaCheckPointController@update');

//AREA CHECK
Route::get('index/area_check/index/{id}', 'AreaCheckController@index');
Route::post('index/area_check/filter_area_check/{id}', 'AreaCheckController@filter_area_check');
Route::post('index/area_check/store/{id}', 'AreaCheckController@store');
Route::get('index/area_check/getareacheck','AreaCheckController@getareacheck')->name('area_check.getareacheck');
Route::post('index/area_check/update/{id}','AreaCheckController@update');
Route::get('index/area_check/destroy/{id}/{area_check_id}', 'AreaCheckController@destroy');
Route::post('index/area_check/print_area_check/{id}','AreaCheckController@print_area_check');
Route::get('index/area_check/print_area_check_email/{id}/{month}','AreaCheckController@print_area_check_email');
Route::post('index/area_check/sendemail/{id}','AreaCheckController@sendemail');
Route::post('index/area_check/approval/{id}/{month}','AreaCheckController@approval');

//JISHU HOZEN POINT
Route::get('index/jishu_hozen_point/index/{id}', 'JishuHozenPointController@index');
Route::get('index/jishu_hozen_point/show/{id}/{jishu_hozen_point_id}', 'JishuHozenPointController@show');
Route::get('index/jishu_hozen_point/destroy/{id}/{jishu_hozen_point_id}', 'JishuHozenPointController@destroy');
Route::post('index/jishu_hozen_point/store/{id}', 'JishuHozenPointController@store');
Route::get('index/jishu_hozen_point/getdetail','JishuHozenPointController@getdetail')->name('jishu_hozen_point.getdetail');
Route::post('index/jishu_hozen_point/update/{id}/{jishu_hozen_point_id}', 'JishuHozenPointController@update');

//JISHU HOZEN
Route::get('index/jishu_hozen/nama_pengecekan/{id}', 'JishuHozenController@nama_pengecekan');
Route::get('index/jishu_hozen/index/{id}/{jishu_hozen_point_id}', 'JishuHozenController@index');
Route::post('index/jishu_hozen/filter_jishu_hozen/{id}/{jishu_hozen_point_id}', 'JishuHozenController@filter_jishu_hozen');
Route::post('index/jishu_hozen/store/{id}/{jishu_hozen_point_id}', 'JishuHozenController@store');
Route::get('index/jishu_hozen/getjishuhozen','JishuHozenController@getjishuhozen')->name('jishu_hozen.getjishuhozen');
Route::post('index/jishu_hozen/update/{id}/{jishu_hozen_point_id}/{jishu_hozen_id}','JishuHozenController@update');
Route::get('index/jishu_hozen/destroy/{id}/{jishu_hozen_point_id}/{jishu_hozen_id}', 'JishuHozenController@destroy');
Route::get('index/jishu_hozen/print_jishu_hozen/{id}/{jishu_hozen_id}/{month}','JishuHozenController@print_jishu_hozen');
Route::get('index/jishu_hozen/print_jishu_hozen_email/{id}/{jishu_hozen_id}/{month}','JishuHozenController@print_jishu_hozen_email');
Route::get('index/jishu_hozen/print_jishu_hozen_approval/{activity_list_id}/{month}','JishuHozenController@print_jishu_hozen_approval');
Route::get('index/jishu_hozen/sendemail/{id}/{jishu_hozen_point_id}','JishuHozenController@sendemail');
Route::post('index/jishu_hozen/approval/{id}/{jishu_hozen_id}/{month}','JishuHozenController@approval');

//APD CHECK
Route::get('index/apd_check/index/{id}', 'ApdCheckController@index');
Route::post('index/apd_check/filter_apd_check/{id}', 'ApdCheckController@filter_apd_check');
Route::post('index/apd_check/store/{id}', 'ApdCheckController@store');
Route::get('index/apd_check/getapdcheck','ApdCheckController@getapdcheck')->name('apd_check.getapdcheck');
Route::post('index/apd_check/update/{id}','ApdCheckController@update');
Route::get('index/apd_check/destroy/{id}/{area_check_id}', 'ApdCheckController@destroy');
Route::post('index/apd_check/print_apd_check/{id}','ApdCheckController@print_apd_check');
Route::get('index/apd_check/print_apd_check_email/{id}/{month}','ApdCheckController@print_apd_check_email');
Route::post('index/apd_check/sendemail/{id}','ApdCheckController@sendemail');
Route::post('index/apd_check/approval/{id}/{month}','ApdCheckController@approval');


//APD
Route::get('index/apd', 'APDController@indexAPD');
Route::get('fetch/apd', 'APDController@fetchAPD');
Route::get('fetch/apd_detail', 'APDController@fetchAPDDetail');
Route::post('input/apd', 'APDController@inputAPD');

//WEEKLY REPORT
Route::get('index/weekly_report/index/{id}', 'WeeklyActivityReportController@index');
Route::post('index/weekly_report/filter_weekly_report/{id}', 'WeeklyActivityReportController@filter_weekly_report');
Route::post('index/weekly_report/store/{id}', 'WeeklyActivityReportController@store');
Route::get('index/weekly_report/getweeklyreport','WeeklyActivityReportController@getweeklyreport')->name('weekly_report.getweeklyreport');
Route::post('index/weekly_report/update/{id}','WeeklyActivityReportController@update');
Route::get('index/weekly_report/destroy/{id}/{area_check_id}', 'WeeklyActivityReportController@destroy');
Route::post('index/weekly_report/print_weekly_report/{id}','WeeklyActivityReportController@print_weekly_report');
Route::get('index/weekly_report/print_weekly_report_email/{id}/{month}','WeeklyActivityReportController@print_weekly_report_email');
Route::post('index/weekly_report/sendemail/{id}','WeeklyActivityReportController@sendemail');
Route::post('index/weekly_report/approval/{id}/{month}','WeeklyActivityReportController@approval');

//NG FINDING
Route::get('index/ng_finding/index/{id}', 'NgFindingController@index');
Route::post('index/ng_finding/filter_ng_finding/{id}', 'NgFindingController@filter_ng_finding');
Route::post('index/ng_finding/store/{id}', 'NgFindingController@store');
Route::get('index/ng_finding/getngfinding','NgFindingController@getngfinding')->name('ng_finding.getngfinding');
Route::post('index/ng_finding/update/{id}/{ng_finding_id}','NgFindingController@update');
Route::get('index/ng_finding/destroy/{id}/{area_check_id}', 'NgFindingController@destroy');
Route::post('index/ng_finding/print_ng_finding/{id}','NgFindingController@print_ng_finding');
Route::get('index/ng_finding/print_ng_finding_email/{id}/{month}','NgFindingController@print_ng_finding_email');
Route::post('index/ng_finding/sendemail/{id}','NgFindingController@sendemail');
Route::post('index/ng_finding/approval/{id}/{month}','NgFindingController@approval');

//RECORDER PUSH BLOCK CHECK
Route::get('index/recorder_process', 'RecorderProcessController@index');
Route::get('index/recorder_process_push_block/{remark}', 'RecorderProcessController@index_push_block');
Route::get('index/fetch_push_block', 'RecorderProcessController@fetch_push_block');
Route::post('index/push_block_recorder/create', 'RecorderProcessController@create');
Route::post('index/push_block_recorder/create_temp', 'RecorderProcessController@create_temp');
Route::post('index/push_block_recorder/update_temp', 'RecorderProcessController@update_temp');
Route::get('index/push_block_recorder/get_temp', 'RecorderProcessController@get_temp');
Route::post('index/push_block_recorder_resume/create_resume', 'RecorderProcessController@create_resume');
Route::get('index/fetchResume', 'RecorderProcessController@fetchResume');
Route::get('index/recorder/report_push_block/{remark}', 'RecorderProcessController@report_push_block');
Route::post('index/recorder/filter_report_push_block/{remark}', 'RecorderProcessController@filter_report_push_block');
Route::get('index/recorder/resume_push_block/{remark}', 'RecorderProcessController@resume_push_block');
Route::post('index/recorder/filter_resume_push_block/{remark}', 'RecorderProcessController@filter_resume_push_block');
Route::get('index/recorder/push_block_check_monitoring/{remark}', 'RecorderProcessController@push_block_check_monitoring');
Route::get('fetch/recorder/push_block_check_monitoring/{remark}', 'RecorderProcessController@fetch_push_block_check_monitoring');
Route::get('fetch/recorder/height_check_monitoring/{remark}', 'RecorderProcessController@fetch_height_check_monitoring');
Route::get('index/recorder/detail_monitoring', 'RecorderProcessController@detail_monitoring');
Route::get('index/recorder/detail_monitoring2', 'RecorderProcessController@detail_monitoring2');
Route::post('index/recorder/print_report_push_block/{remark}', 'RecorderProcessController@print_report_push_block');
Route::get('index/recorder/get_push_pull','RecorderProcessController@get_push_pull')->name('recorder.get_push_pull');
Route::post('index/recorder/update/{id}','RecorderProcessController@update');

//MACHINE PARAMETER
Route::get('index/machine_parameter','RecorderProcessController@indexMachineParameter');
Route::post('index/filter_machine_parameter','RecorderProcessController@filterMachineParameter');
Route::get('index/fetch_mesin_parameter', 'RecorderProcessController@fetch_mesin_parameter');
Route::post('index/push_block_recorder/create_parameter', 'RecorderProcessController@create_parameter');
Route::get('index/push_block_recorder/get_parameter','RecorderProcessController@get_parameter')->name('recorder.get_parameter');
Route::post('index/push_block_recorder/update_parameter/{id}', 'RecorderProcessController@update_parameter');
Route::get('index/push_block_recorder/delete_parameter/{id}', 'RecorderProcessController@delete_parameter');

//RECORDER TORQUE CHECK
Route::get('index/recorder_process_torque/{remark}', 'RecorderProcessController@index_torque');

//RECORDER PUSH PULL CHECK
Route::get('index/recorder_push_pull_check', 'RecorderProcessController@index_push_pull');
Route::get('push_pull/fetchResult', 'RecorderProcessController@fetchResultPushPull');
Route::get('push_pull/fetchResultCamera', 'RecorderProcessController@fetchResultCamera');
Route::get('post/display/email/{value_check}/{judgement}/{model}/{checked_at}/{pic_check}/{remark}', 'RecorderProcessController@email');
Route::post('push_pull/store_push_pull', 'RecorderProcessController@store_push_pull');
Route::post('camera_kango/store_camera_kango', 'RecorderProcessController@store_camera');
Route::post('camera_kango/store_camera_kango2', 'RecorderProcessController@store_camera2');
Route::get('scan/push_pull/operator', 'RecorderProcessController@scanPushPullOperator');
Route::get('index/recorder/resume_assy_rc', 'RecorderProcessController@index_resume_assy_rc');
Route::post('recorder/filter_assy_rc', 'RecorderProcessController@filter_assy_rc');
Route::get('index/recorder/rc_picking_result', 'RecorderProcessController@index_rc_picking_result');
Route::get('fetch/recorder/rc_picking_result', 'RecorderProcessController@fetch_rc_picking_result');

//WEBCAM
Route::get('index/webcam', 'WebcamController@index');
Route::post('index/webcam/create', 'WebcamController@create');

Route::group(['nav' => 'M21', 'middleware' => 'permission'], function(){

	//CPAR
	Route::get('index/qc_report', 'QcReportController@index');
	Route::get('index/qc_report/create', 'QcReportController@create');
	Route::post('index/qc_report/create_action', 'QcReportController@create_action');
	Route::get('index/qc_report/update/{id}', 'QcReportController@update');
	Route::post('index/qc_report/update_action/{id}', 'QcReportController@update_action');
	Route::post('index/qc_report/update_deskripsi/{id}', 'QcReportController@update_deskripsi');
	Route::get('index/qc_report/delete/{id}', 'QcReportController@delete');
	Route::post('index/qc_report/create_item', 'QcReportController@create_item');
	Route::get('index/qc_report/fetch_item/{id}', 'QcReportController@fetch_item');
	Route::post('index/qc_report/edit_item', 'QcReportController@edit_item');
	Route::get('index/qc_report/edit_item', 'QcReportController@fetch_item_edit');
	Route::get('index/qc_report/view_item', 'QcReportController@view_item');
	Route::post('index/qc_report/delete_item', 'QcReportController@delete_item');
	Route::post('index/qc_report/deletefiles', 'QcReportController@deletefiles');
	Route::get('index/qc_report/print_cpar/{id}', 'QcReportController@print_cpar');
	Route::get('index/qc_report/print_cpar_new/{id}', 'QcReportController@print_cpar_new');
	Route::get('index/qc_report/sendemail/{id}/{posisi}', 'QcReportController@sendemail');

	Route::get('index/qc_report/verifikasigm/{id}', 'QcReportController@verifikasigm');
	Route::get('index/qc_report/sign', 'QcReportController@sign');
	Route::post('index/qc_report/save_sign', 'QcReportController@save_sign');

	//verifikasi CPAR
	Route::get('index/qc_report/statuscpar/{id}', 'QcReportController@statuscpar');
	Route::get('index/qc_report/verifikasicpar/{id}', 'QcReportController@verifikasicpar');
	Route::post('index/qc_report/checked/{id}', 'QcReportController@checked');
	Route::post('index/qc_report/unchecked/{id}', 'QcReportController@unchecked');
	
	//CAR
	Route::get('index/qc_car', 'QcCarController@index');
	Route::post('index/qc_car/filter', 'QcCarController@filter_data');
	Route::get('index/qc_car/detail/{id}', 'QcCarController@detail');
	Route::post('index/qc_car/create_pic/{id}', 'QcCarController@create_pic');
	Route::post('index/qc_car/detail_action/{id}', 'QcCarController@detail_action');
	Route::get('index/qc_car/print_car/{id}', 'QcCarController@print_car');
	Route::get('index/qc_car/print_car_new/{id}', 'QcCarController@print_car2');
	Route::get('index/qc_car/coba_print/{id}', 'QcCarController@coba_print');
	Route::get('index/qc_car/sendemail/{id}/{posisi}', 'QcCarController@sendemail');
	Route::post('index/qc_car/deletefiles', 'QcCarController@deletefiles');
	Route::get('index/qc_car/verifikasigm/{id}', 'QcCarController@verifikasigm');
	Route::post('index/qc_car/save_sign', 'QcCarController@save_sign');
	
	//Verifikator CAR
	Route::get('index/qc_car/verifikator', 'QcCarController@verifikator');

	//Verifikasi CAR
	Route::get('index/qc_car/verifikasicar/{id}', 'QcCarController@verifikasicar');
	Route::post('index/qc_car/checked/{id}', 'QcCarController@checked');
	Route::post('index/qc_car/unchecked/{id}', 'QcCarController@unchecked');
	Route::post('index/qc_car/uncheckedGM/{id}', 'QcCarController@uncheckedGM');

	//Verifikasi QA
	Route::get('index/qc_report/verifikasiqa/{id}', 'QcReportController@verifikasiqa');
	Route::post('index/qc_report/close1/{id}', 'QcReportController@close1');
	Route::get('index/qc_report/emailverification/{id}', 'QcReportController@emailverification');
	Route::post('index/qc_report/close2/{id}', 'QcReportController@close2');
	Route::post('index/qc_report/deleteVerifikasi', 'QcReportController@deleteVerifikasi');
});

//CPAR
Route::get('index/cpar/resume', 'QcReportController@resume');
Route::get('fetch/cpar/resume', 'QcReportController@getResumeData');

Route::get('index/qc_report/get_fiscal_year', 'QcReportController@get_fiscal');
Route::get('index/qc_report/get_nomor_depan', 'QcReportController@get_nomor_depan');
Route::get('index/qc_report/grafik_cpar', 'QcReportController@grafik_cpar');
Route::get('index/qc_report/grafik_kategori', 'QcReportController@grafik_kategori');
Route::get('index/qc_report/komplain_monitoring', 'QcReportController@komplain_monitoring');
Route::get('index/qc_report/komplain_monitoring2', 'QcReportController@komplain_monitoring2');
Route::get('index/qc_report/komplain_monitoring3', 'QcReportController@komplain_monitoring3');
Route::get('index/qc_report/komplain_monitoring4', 'QcReportController@komplain_monitoring4');
Route::get('index/qc_report/komplain_monitoring5', 'QcReportController@komplain_monitoring5');
Route::get('index/qc_report/fetchReport', 'QcReportController@fetchReport');
Route::get('index/qc_report/fetchKategori', 'QcReportController@fetchKategori');
Route::get('index/qc_report/fetchSource', 'QcReportController@fetchSource');
Route::get('index/qc_report/fetchEksternal', 'QcReportController@fetchEksternal');
Route::get('index/qc_report/fetchSupplier', 'QcReportController@fetchSupplier');
Route::get('index/qc_report/detail_cpar', 'QcReportController@detail_cpar');
Route::get('index/qc_report/detail_kategori', 'QcReportController@detail_kategori');
Route::get('index/qc_report/detail_cpar_dept', 'QcReportController@detail_cpar_dept');
Route::get('index/qc_report/detail_monitoring', 'QcReportController@detail_monitoring');
Route::post('index/qc_report/filter_cpar', 'QcReportController@filter_cpar');
Route::get('index/qc_report/get_detailmaterial', 'QcReportController@getmaterialsbymaterialsnumber')->name('admin.getmaterialsbymaterialsnumber');
Route::get('index/qc_report/fetchtable', 'QcReportController@fetchtable');
Route::get('index/qc_report/fetchMonitoring', 'QcReportController@fetchMonitoring');
Route::get('index/qc_report/fetchGantt', 'QcReportController@fetchGantt');

// Form Ketidaksesuaian YMMJ
Route::get('index/qa_ymmj', 'QcYmmjController@index');
Route::post('index/qa_ymmj/form', 'QcYmmjController@filter');
Route::get('index/qa_ymmj/create', 'QcYmmjController@create');
Route::post('index/qa_ymmj/create_action', 'QcYmmjController@create_action');
Route::get('index/qa_ymmj/update/{id}', 'QcYmmjController@update');
Route::post('index/qa_ymmj/update_action/{id}', 'QcYmmjController@update_action');
Route::post('index/qa_ymmj/deletefiles', 'QcYmmjController@deletefiles');
Route::get('index/qa_ymmj/grafik_ymmj', 'QcYmmjController@grafik_ymmj');
Route::get('index/qa_ymmj/fetchGrafik', 'QcYmmjController@fetchGrafik');
Route::get('index/qa_ymmj/fetchtable', 'QcYmmjController@fetchTable');
Route::get('index/qa_ymmj/detail', 'QcYmmjController@detail');
Route::get('index/qa_ymmj/print/{id}', 'QcYmmjController@print_ymmj');

// Request CPAR QA

Route::get('index/request_qa', 'QcRequestController@index');
Route::get('index/request_qa/create', 'QcRequestController@create');
Route::post('index/request_qa/create_action', 'QcRequestController@create_action');
Route::post('index/request_qa/update_action/{id}', 'QcRequestController@update_action');
Route::get('index/request_qa/detail/{id}', 'QcRequestController@detail');
Route::get('index/request_qa/fetchDataTable', 'QcRequestController@fetchDataTable');
Route::get('index/request_qa/fetch_item/{id}', 'QcRequestController@fetch_item');
Route::post('index/request_qa/create_item', 'QcRequestController@create_item');
Route::post('index/request_qa/edit_item', 'QcRequestController@edit_item');
Route::post('index/request_qa/update_detail/{id}', 'QcRequestController@update_detail');
Route::get('index/request_qa/edit_item', 'QcRequestController@fetch_item_edit');
Route::post('index/request_qa/delete_item', 'QcRequestController@delete_item');
Route::get('index/request_qa/print/{id}', 'QcRequestController@print_report');
Route::post('index/request_qa/approval/{id}', 'QcRequestController@approval');
Route::get('index/request_qa/verifikasi/{id}', 'QcRequestController@verifikasi');

// CPAR Antar Departemen & Bagian
Route::get('index/form_ketidaksesuaian', 'CparController@index');
Route::get('index/form_ketidaksesuaian/fetchDataTable', 'CparController@fetchDataTable');
Route::get('index/form_ketidaksesuaian/create', 'CparController@create');
Route::post('post/form_ketidaksesuaian/create', 'CparController@post_create');
Route::get('index/form_ketidaksesuaian/detail/{id}', 'CparController@detail');
Route::get('index/form_ketidaksesuaian/fetch_item/{id}', 'CparController@fetch_item');
Route::post('index/form_ketidaksesuaian/create_item', 'CparController@create_item');
Route::get('index/form_ketidaksesuaian/edit_item', 'CparController@fetch_item_edit');
Route::post('index/form_ketidaksesuaian/edit_item', 'CparController@edit_item');
Route::post('index/form_ketidaksesuaian/delete_item', 'CparController@delete_item');
Route::post('index/form_ketidaksesuaian/update_detail/{id}', 'CparController@update_detail');
Route::get('index/form_ketidaksesuaian/print/{id}', 'CparController@print_report');
// Verifikasi CPAR Departemen
Route::get('index/form_ketidaksesuaian/verifikasicpar/{id}', 'CparController@verifikasicpar');
Route::post('index/form_ketidaksesuaian/approval/{id}', 'CparController@approval');
Route::post('index/form_ketidaksesuaian/notapprove/{id}', 'CparController@notapprove');
Route::get('index/form_ketidaksesuaian/sendemail/{id}', 'CparController@sendemail');
Route::get('index/form_ketidaksesuaian/sendemailqa/{id}', 'CparController@sendemailqa');
// CAR Antar Departemen
Route::get('index/form_ketidaksesuaian/response/{id}', 'CparController@response');
Route::post('index/form_ketidaksesuaian/update_car/{id}', 'CparController@update_car');
// Verifikasi CAR Departemen & Bagian
Route::get('index/form_ketidaksesuaian/verifikasicar/{id}', 'CparController@verifikasicar');
Route::post('index/form_ketidaksesuaian/approvalcar/{id}', 'CparController@approvalcar');
Route::post('index/form_ketidaksesuaian/notapprovecar/{id}', 'CparController@notapprovecar');
Route::get('index/form_ketidaksesuaian/sendemailcar/{id}', 'CparController@sendemailcar');
// Verifikasi Bagian
Route::get('index/form_ketidaksesuaian/verifikasibagian/{id}', 'CparController@verifikasibagian');
Route::post('index/form_ketidaksesuaian/close', 'CparController@closecar');
Route::post('index/form_ketidaksesuaian/reject', 'CparController@rejectcar');
//Monitoring CPAR
Route::get('index/form_ketidaksesuaian/monitoring', 'CparController@monitoring');
Route::get('fetch/form_ketidaksesuaian/monitoring', 'CparController@fetchMonitoring');
Route::get('index/form_ketidaksesuaian/detail', 'CparController@detailMonitoring');
Route::get('index/form_ketidaksesuaian/table', 'CparController@fetchTable');
//approve or Reject CPAR By QA
Route::get('index/form_ketidaksesuaian/approveqa/{id}', 'CparController@approveqa');
Route::get('index/form_ketidaksesuaian/rejectqa/{id}', 'CparController@rejectqa');


//Audit Internal ISO
Route::get('index/audit_iso', 'CparController@audit');
Route::get('index/audit_iso/fetchDataTable', 'CparController@fetchDataAudit');
Route::get('index/audit_iso/create', 'CparController@audit_create');
Route::post('post/audit_iso/create', 'CparController@audit_post_create');
Route::get('index/audit_iso/get_nama', 'CparController@audit_get_nama');
Route::get('index/audit_iso/get_nomor_depan', 'CparController@audit_get_nomor');
Route::get('index/audit_iso/detail/{id}', 'CparController@audit_detail');
Route::post('post/audit_iso/detail', 'CparController@audit_post_detail');
Route::post('post/audit_iso/detail_last', 'CparController@audit_post_detail_last');
Route::get('index/audit_iso/verifikasistd/{id}', 'CparController@verifikasistd');
Route::post('index/audit_iso/approval/{id}', 'CparController@std_approval');
Route::post('index/audit_iso/comment/{id}', 'CparController@std_comment');
Route::post('index/audit_iso/reject/{id}', 'CparController@std_reject');
Route::get('index/audit_iso/response/{id}', 'CparController@std_response');
Route::post('index/audit_iso/update_response/{id}', 'CparController@update_response');
Route::get('index/audit_iso/sendemailpenanganan/{id}', 'CparController@sendemailpenanganan');
Route::get('index/audit_iso/print/{id}', 'CparController@print_audit');

//CUBEACON WAREHOUSE
Route::get('mqtt/publish/{topic}/{message}', 'TrialController@SendMsgViaMqtt');
Route::get('mqtt/publish/{topic}', 'TrialController@SubscribetoTopic');
Route::get('index/beacon','BeaconController@index');
Route::get('fetch/user/beacon','BeaconController@getUser');
Route::get('index/master_beacon','BeaconController@master_beacon');
Route::post('index/master_beacon/daftar', 'BeaconController@daftar');
Route::get('index/master_beacon/edit','BeaconController@edit')->name('admin.beaconedit');
Route::get('index/master_beacon/delete/{id}','BeaconController@delete');

//CUBEACON REEDPLATE
Route::get('index/reedplate/map','ReedplateController@index');
Route::get('index/reedplate/working_time','ReedplateController@reed');
Route::get('fetch/reedplate/user','ReedplateController@getUser');
Route::get('fetch/reedplate/log','ReedplateController@fetch_log');
Route::post('index/reedplate/reader', 'ReedplateController@inputTemp');

//TEMPERATURE / SUHU
Route::get('index/grafikServer','TemperatureController@grafikServer');
Route::get('index/log_map_server','TemperatureController@log_map_server');
Route::get('index/data_suhu_server','TemperatureController@data_suhu_server');
Route::get('index/index_map','TemperatureController@index_map');
Route::get('index/grafikOffice','TemperatureController@grafikOffice');
Route::get('index/data_suhu_office','TemperatureController@data_suhu_office');
Route::get('index/log_map_office','TemperatureController@log_map_office');
Route::get('index/standart_temperature', 'TemperatureController@standart');
Route::get('index/temperature/edit','TemperatureController@edit')->name('admin.temperaturedit');
Route::post('index/temperature/aksi_edit', 'TemperatureController@aksi_edit');
Route::get('index/temperature/delete/{id}','TemperatureController@delete');



// BUFFING TOILET
Route::get('index/toilet', 'RoomController@indexToilet');
Route::get('index/room/toilet/{id}', 'RoomController@indexRoomToilet');
Route::get('fetch/room/toilet', 'RoomController@fetchToilet');

//PRESS
Route::get('index/press/create', 'PressController@create');
Route::get('index/press/fl', 'PressController@index_fl');
Route::get('index/press/cl', 'PressController@index_cl');
Route::get('index/press/vn', 'PressController@index_vn');
Route::get('scan/press/operator', 'PressController@scanPressOperator');
Route::get('fetch/press/press_list', 'PressController@fetchPressList');
Route::get('fetch/press/trouble_list', 'PressController@fetchTroubleList');
Route::get('fetch/press/fetchMaterialList', 'PressController@fetchMaterialList');
Route::get('fetch/press/fetchPunch', 'PressController@fetchPunch');
Route::get('fetch/press/fetchDie', 'PressController@fetchDie');
Route::get('fetch/press/fetchProcess', 'PressController@fetchProcess');
Route::post('index/press/store', 'PressController@store');
Route::post('index/press/store_fl', 'PressController@store_fl');
Route::post('index/press/store_cl', 'PressController@store_cl');
Route::post('index/press/store_vn', 'PressController@store_vn');
Route::post('index/press/store_kanagata', 'PressController@store_kanagata');
Route::post('index/press/store_trouble', 'PressController@store_trouble');
Route::post('index/press/finish_trouble', 'PressController@finish_trouble');
//Display Press
Route::get('index/press/monitoring', 'PressController@monitoring');
Route::get('fetch/press/monitoring', 'PressController@fetchMonitoring');
Route::get('index/press/detail_press', 'PressController@detail_press');
Route::get('index/press/detail_pic', 'PressController@detail_pic');
Route::get('index/press/monitoring2', 'PressController@monitoring2');
//Report Press
Route::get('index/press/report_trouble', 'PressController@report_trouble');
Route::post('index/press/filter_report_trouble', 'PressController@filter_report_trouble');
Route::get('index/press/report_prod_result', 'PressController@report_prod_result');
Route::post('index/press/filter_report_prod_result', 'PressController@filter_report_prod_result');
Route::get('index/press/report_kanagata_lifetime', 'PressController@report_kanagata_lifetime');
Route::post('index/press/filter_report_kanagata_lifetime', 'PressController@filter_report_kanagata_lifetime');
Route::get('index/kanagata_lifetime/getkanagatalifetime','PressController@getkanagatalifetime')->name('kanagata_lifetime.getkanagatalifetime');
Route::post('index/kanagata/update/{id}','PressController@updateKanagataLifetime');
Route::post('index/kanagata/reset','PressController@reset');
Route::get('index/prod_result/getprodresult','PressController@getprodresult')->name('prod_result.getprodresult');
Route::post('index/prod_result/update/{id}','PressController@updateProdResult');
//Master Kanagata
Route::get('index/press/master_kanagata', 'PressController@indexMasterKanagata');
Route::get('fetch/press/master_kanagata', 'PressController@fetchMasterKanagata');
Route::post('post/press/add_kanagata', 'PressController@addKanagata');
Route::get('index/press/destroy_kanagata/{id}', 'PressController@destroyKanagata');
Route::get('fetch/press/get_kanagata', 'PressController@getKanagata');
Route::post('post/press/update_kanagata', 'PressController@updateKanagata');

//Form Failure
Route::get('index/form_experience', 'FormExperienceController@index');
Route::post('index/form_experience/filter', 'FormExperienceController@filter_form');
Route::get('index/form_experience/create', 'FormExperienceController@create');
Route::post('index/post/form_experience', 'FormExperienceController@post_form');
Route::get('index/form_experience/edit/{id}', 'FormExperienceController@update');
Route::post('index/update/form_experience', 'FormExperienceController@update_form');
Route::get('index/form_experience/print/{id}', 'FormExperienceController@print_form');
Route::get('index/form_experience/get_nama', 'FormExperienceController@get_nik');

//IP
Route::get('index/display/ip', 'PingController@indexIpMonitoring');
Route::get('fetch/display/ip', 'PingController@fetch');
Route::get('fetch/display/fetch_hit/{ip}', 'PingController@fetch_hit');
Route::post('post/display/ip_log', 'PingController@ip_log');

//OFFICECLOCK
Route::get('index/display/office_clock', 'OfficeClockController@index');
Route::get('fetch/office_clock/visitor', 'OfficeClockController@fetchVisitor');
Route::get('index/display/office_clock2', 'OfficeClockController@index2');
Route::get('fetch/office_clock/visitor2', 'OfficeClockController@fetchVisitor2');
Route::get('index/display/office_clock3', 'OfficeClockController@index3');
Route::get('fetch/office_clock/visitor3', 'OfficeClockController@fetchVisitor3');
Route::get('index/display/guest_room', 'OfficeClockController@guest_room');
Route::get('index/display/guest_room2', 'OfficeClockController@guest_room2');
Route::get('fetch/office_clock/weather', 'OfficeClockController@fetchWeather');

//MAINTENANCE
Route::group(['nav' => 'S34', 'middleware' => 'permission'], function(){
	Route::get('index/maintenance/list/user', 'MaintenanceController@indexMaintenanceForm');
	Route::get('fetch/maintenance/list_spk/user', 'MaintenanceController@fetchMaintenance');
	Route::post('create/maintenance/spk', 'MaintenanceController@createSPK');

	Route::get('index/maintenance/list_spk', 'MaintenanceController@indexMaintenanceList');
	Route::get('fetch/maintenance/list_spk', 'MaintenanceController@fetchMaintenanceList');
	Route::get('fetch/maintenance/detail', 'MaintenanceController@fetchMaintenanceDetail');

	Route::post('post/maintenance/member', 'MaintenanceController@postMemberSPK');
	Route::get('index/maintenance/spk', 'MaintenanceController@indexSPK');

	Route::get('fetch/maintenance/spk', 'MaintenanceController@fetchSPK');

	// -----------  APAR -----------
	Route::get('index/maintenance/apar', 'MaintenanceController@indexApar');
	Route::get('index/maintenance/aparTool', 'MaintenanceController@indexAparTool');
	Route::get('index/maintenance/aparCheck', 'MaintenanceController@indexAparCheck');
	Route::get('index/maintenance/apar/expire', 'MaintenanceController@indexAparExpire');
	Route::get('index/maintenance/apar/resume', 'MaintenanceController@indexAparResume');
	Route::get('index/maintenance/apar/uses', 'MaintenanceController@indexAparUses');

	Route::get('fetch/maintenance/apar/list', 'MaintenanceController@fetchAparList');
	Route::get('fetch/maintenance/apar/history', 'MaintenanceController@fetchAparCheck');
	Route::get('fetch/maintenance/apar/expire', 'MaintenanceController@fetchAparExpire');
	Route::get('fetch/maintenance/apar/list/check', 'MaintenanceController@fetchAparCheck2');
	Route::get('fetch/maintenance/apar/list/monitoring', 'MaintenanceController@fetch_apar_monitoring');
	Route::get('fetch/maintenance/hydrant/list/monitoring', 'MaintenanceController@fetch_hydrant_monitoring');
	Route::get('fetch/maintenance/apar/resume', 'MaintenanceController@fetch_apar_resume');
	Route::get('fetch/maintenance/apar/resumeWeek', 'MaintenanceController@fetch_apar_resume_week');
	Route::get('fetch/maintenance/apar/resume/detail/week', 'MaintenanceController@fetch_apar_resume_detail_week');
	Route::get('fetch/maintenance/apar/resume/detail', 'MaintenanceController@fetch_apar_resume_detail');
	Route::get('fetch/maintenance/apar/use/list', 'MaintenanceController@fetch_apar_use');
	Route::get('fetch/maintenance/apar/use/check', 'MaintenanceController@check_apar_use');
	// Route::get('fetch/maintenance/apar/byCode', 'MaintenanceController@fetchAparbyCode');

	Route::post('post/maintenance/apar/check', 'MaintenanceController@postCheck');
	Route::post('post/maintenance/apar/insert', 'MaintenanceController@createTool');
	Route::post('post/maintenance/apar/replace', 'MaintenanceController@replaceTool');
	Route::post('use/maintenance/apar', 'MaintenanceController@check_apar_use');

	Route::get('print/apar/qr/{apar_id}/{apar_name}/{exp_date}/{last_check}/{hasil_check}', 'MaintenanceController@print_apar2');
});

//Assemblies
Route::get('index/kensa/{location}', 'AssemblyProcessController@kensa');
Route::get('scan/assembly/operator', 'AssemblyProcessController@scanAssemblyOperator');
Route::get('scan/assembly/kensa', 'AssemblyProcessController@scanAssemblyKensa');
Route::post('input/assembly/kensa', 'AssemblyProcessController@inputAssemblyKensa');
Route::get('fetch/assembly/ng_detail', 'AssemblyProcessController@showNgDetail');
Route::get('fetch/assembly/onko', 'AssemblyProcessController@fetchOnko');
Route::get('fetch/assembly/ng_temp', 'AssemblyProcessController@fetchNgTemp');
Route::get('fetch/assembly/ng_temp_by_id', 'AssemblyProcessController@fetchNgTempById');
Route::post('input/assembly/ng_temp', 'AssemblyProcessController@inputNgTemp');
Route::get('delete/assembly/delete_ng_temp', 'AssemblyProcessController@deleteNgTemp');
Route::post('input/assembly/ng_onko', 'AssemblyProcessController@inputNgOnko');
Route::get('fetch/assembly/get_process_before', 'AssemblyProcessController@getProcessBefore');
Route::get('index/assembly/flute/print_label', 'AssemblyProcessController@indexFlutePrintLabel');

Route::get('index/board/{location}', 'AssemblyProcessController@indexAssemblyBoard');
Route::get('fetch/assembly/board', 'AssemblyProcessController@fetchAssemblyBoard');

Route::get('index/assembly/request/display/{id}', 'AssemblyProcessController@indexRequestDisplay');
Route::get('fetch/assembly/request', 'AssemblyProcessController@fetchRequest');

Route::get('index/assembly/ng_rate', 'AssemblyProcessController@indexNgRate');
Route::get('fetch/assembly/ng_rate', 'AssemblyProcessController@fetchNgRate');

Route::get('index/assembly/op_ng', 'AssemblyProcessController@indexOpRate');
Route::get('fetch/assembly/op_ng', 'AssemblyProcessController@fetchOpRate');

//ROOMS
Route::get('/meetingroom1', function () {
	return view('rooms.meetingroom1');
});
Route::get('/fillingroom', function () {
	return view('rooms.fillingroom');
});
Route::get('/trainingroom1', function () {
	return view('rooms.trainingroom1');
});
Route::get('/trainingroom2', function () {
	return view('rooms.trainingroom2');
});
Route::get('/trainingroom3', function () {
	return view('rooms.trainingroom3');
});

Route::get('/welcome_trial', function () {
	return view('trials.welcome_trial');
});


// MIRAI MOBILE
Route::get('index/mirai_mobile/index', 'MiraiMobileController@index');

// CORONA
Route::get('index/corona_information', 'MiraiMobileController@indexCoronaInformation');
Route::get('fetch/corona_information', 'MiraiMobileController@fetchCoronaInformation');

//Display Health
Route::get('index/mirai_mobile/healthy_report', 'MiraiMobileController@display_health');
Route::get('fetch/mirai_mobile/healthy_report', 'MiraiMobileController@fetch_health');
Route::get('index/mirai_mobile/detail', 'MiraiMobileController@fetch_detail');
Route::get('index/mirai_mobile/detail_sakit', 'MiraiMobileController@fetch_detail_sakit');

//report attendance
Route::get('index/mirai_mobile/report_attendance', 'MiraiMobileController@health');
Route::get('fetch/mirai_mobile/report_attendance', 'MiraiMobileController@fetchHealthData');
Route::get('fetch/location_employee', 'MiraiMobileController@fetchLocationEmployee');
Route::get('index/mirai_mobile/report_attendance_sbh', 'MiraiMobileController@healthSbh');
Route::get('fetch/mirai_mobile/report_attendance_sbh', 'MiraiMobileController@fetchHealthDataSbh');

//report shift
Route::get('index/mirai_mobile/report_shift', 'MiraiMobileController@shift');
Route::get('fetch/mirai_mobile/report_shift', 'MiraiMobileController@fetchShiftData');

//report location
Route::get('index/mirai_mobile/report_location', 'MiraiMobileController@location');
Route::get('fetch/mirai_mobile/report_location', 'MiraiMobileController@fetchLocation');
Route::get('fetch/mirai_mobile/report_location/detail', 'MiraiMobileController@fetchLocationDetail');
Route::get('fetch/mirai_mobile/report_location/detail_all', 'MiraiMobileController@fetchLocationDetailAll');
Route::get('export/mirai_mobile/report_location', 'MiraiMobileController@exportList');

//report shift
Route::get('index/mirai_mobile/report_indication', 'MiraiMobileController@indication');
Route::get('fetch/mirai_mobile/report_indication', 'MiraiMobileController@fetchIndicationData');

Route::get('/radar_covid', function () {
	return view('mirai_mobile.radar_covid');
});

View::composer('*', function ($view) {
	$controller = new \App\Http\Controllers\EmployeeController;
	$notif = $controller->getNotif();
	$controller_visitor = new \App\Http\Controllers\VisitorController;
	$notif_visitor = $controller_visitor->getNotifVisitor();
	$view->with('notif', $notif)->with('notif_visitor', $notif_visitor);
});