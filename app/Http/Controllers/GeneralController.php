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
		->first();

		if($attendance == ""){
			$response = array(
				'status' => false,
				'message' => 'Karyawan tidak ada pada schedule.'
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

			$query = "SELECT DISTINCT
			employee_id,
			due_date,
			NAME,
			department,
			attend_date 
			FROM
			(
			SELECT
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
