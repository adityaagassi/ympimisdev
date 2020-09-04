<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\GeneralAttendance;
use App\GeneralAttendanceLog;
use App\Employee;
use Response;

class GeneralController extends Controller
{

	public function indexOmiVisitor(){
		$title = 'Koyami Visitor';
		$title_jp = '売店来客';

		return view('general.omi_visitor', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('head', 'Koyami Visitor');
	}

	public function fetchOmiVisitor(){
		$visitors = db::connection('rfid')->table('omi_lists')->get();

		$response = array(
			'status' => true,
			'visitors' => $visitors
		);
		return Response::json($response);
	}

	public function indexGeneralPointingCall($id){
		if($id == 'japanese'){
			$title = 'Japanese Pointing Calls';
			$title_jp = '駐在員指差し呼称';

			return view('general.pointing_call.japanese', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'default_language' => 'jp',
				'location' => $id
			))->with('head', 'Pointing Calls');
		}
	}

	public function editGeneralPointingCallPic(Request $request){

		$pics = db::table('pointing_calls')
		->where('location', '=', $request->get('location'))
		->where('point_title', '=', $request->get('point_title'))
		->update([
			'remark' => 0
		]);

		$pic = db::table('pointing_calls')
		->where('id', '=', $request->get('id'))
		->update([
			'remark' => 1
		]);

		$response = array(
			'status' => true
		);
		return Response::json($response);


	}

	public function fetchGeneralPointingCall(Request $request){
		$pics = db::table('pointing_calls')
		->where('location', '=', $request->get('location'))
		->where('point_title', '=', 'pic')
		->whereNull('deleted_at')
		->get();

		$pointing_calls = db::table('pointing_calls')
		->where('location', '=', $request->get('location'))
		->where('point_title', '<>', 'pic')
		->where('remark', '1')
		->whereNull('deleted_at')
		->get();

		$response = array(
			'status' => true,
			'pointing_calls' => $pointing_calls,
			'pics' => $pics
		);
		return Response::json($response);
	}

	public function indexGeneralAttendanceCheck(){
		$title = "Attendance Check";
		$title_jp = "";

		$purposes = GeneralAttendance::orderBy('purpose_code', 'asc')
		->select('purpose_code')
		->distinct()
		->get();

		return view('general.attendance_check', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'purposes' => $purposes
		))->with('head', 'GA Control')->with('page', 'Driver Control');
	}

	public function scanGeneralAttendanceCheck(Request $request){
		$employee = Employee::where('tag', '=', $request->get('tag'))->first();

		if($employee == ""){
			$response = array(
				'status' => false,
				'message' => 'Tag karyawan tidak terdaftar, hubungi bagian MIS.'
			);
			return Response::json($response);
		}

		$attendance = GeneralAttendance::where('employee_id', '=', $employee->employee_id)
		->where('purpose_code', '=', $request->get('purpose_code'))
		->where('due_date', '=', date('Y-m-d'))
		->first();

		if($attendance == "" || $attendance->due_date > date('Y-m-d')){
			$response = array(
				'status' => false,
				'message' => 'Karyawan tidak ada pada schedule.'
			);
			return Response::json($response);
		}

		if($attendance->attend_date != null){
			$response = array(
				'status' => false,
				'message' => 'Karyawan sudah menghadiri schedule.'
			);
			return Response::json($response);
		}

		try{
			$attendance->attend_date = date('Y-m-d H:i:s');
			$attendance->save();

			$response = array(
				'status' => true,
				'message' => $employee->name.' berhasil hadir.'
			);
			return Response::json($response);

		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'Berhasil'
		);
		return Response::json($response);

	}

	public function fetchGeneralAttendanceCheck(Request $request){

		if(strlen($request->get('purpose_code')) == 0){
			$response = array(
				'status' => false,
				'message' => 'Silahkan memilih kode purpose'
			);
			return Response::json($response);
		}

		try{
			$now = date('Y-m-d');
			// $now = '2020-08-19';

			$query = "SELECT DISTINCT
			purpose_code,
			employee_id,
			due_date,
			NAME,
			departments.department_shortname AS department,
			attend_date 
			FROM
			(
			SELECT
			general_attendances.purpose_code,
			general_attendances.employee_id,
			general_attendances.due_date,
			employee_syncs.`name`,
			employee_syncs.department,
			DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
			FROM
			general_attendances
			LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
			WHERE
			general_attendances.due_date = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."' UNION ALL
			SELECT
			general_attendances.purpose_code,
			general_attendances.employee_id,
			general_attendances.due_date,
			employee_syncs.`name`,
			employee_syncs.department,
			DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
			FROM
			general_attendances
			LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
			WHERE
			DATE( general_attendances.attend_date ) = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."'
			) AS attendances 
			LEFT JOIN
			departments on departments.department_name = attendances.department
			WHERE employee_id like 'PI%'
			ORDER BY
			attend_date DESC,
			NAME ASC";

			$attendance_lists = db::select($query); 

			$response = array(
				'status' => true,
				'attendance_lists' => $attendance_lists
			);
			return Response::json($response);

		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}		
	}
}
