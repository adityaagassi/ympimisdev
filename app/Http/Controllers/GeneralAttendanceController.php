<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Mail\SendEmail;
use App\GeneralAttendance;
use App\GeneralAttendanceLog;
use App\Employee;
use App\EmployeeSync;
use App\GeneralTransportation;
use App\GeneralTransportationData;
use App\GeneralDoctor;
use App\CodeGenerator;
use App\GeneralShoesLog;
use App\GeneralShoesRequest;
use App\GeneralShoesStock;
use App\User;
use App\Agreement;
use App\SafetyRiding;
use App\AgreementAttachment;
use App\GeneralAirVisualLog;
use App\WeeklyCalendar;
use PDF;
use Auth;
use Excel;
use DataTables;
use Response;

class GeneralAttendanceController extends Controller
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

		try{
			$now = date('Y-m-d');

			$attendance_lists = db::select("SELECT
				ga.purpose_code,
				ga.employee_id,
				es.`name`,
				ga.attend_date 
				FROM
				general_attendances AS ga
				LEFT JOIN employee_syncs AS es ON ga.employee_id = es.employee_id 
				WHERE
				ga.due_date = '".$now."' 
				ORDER BY
				attend_date DESC");

			// $query = "SELECT DISTINCT
			// purpose_code,
			// employee_id,
			// due_date,
			// NAME,
			// departments.department_shortname AS department,
			// attend_date 
			// FROM
			// (
			// SELECT
			// general_attendances.purpose_code,
			// general_attendances.employee_id,
			// general_attendances.due_date,
			// employee_syncs.`name`,
			// employee_syncs.department,
			// DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
			// FROM
			// general_attendances
			// LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
			// WHERE
			// general_attendances.due_date = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."' UNION ALL
			// SELECT
			// general_attendances.purpose_code,
			// general_attendances.employee_id,
			// general_attendances.due_date,
			// employee_syncs.`name`,
			// employee_syncs.department,
			// DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
			// FROM
			// general_attendances
			// LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
			// WHERE
			// DATE( general_attendances.attend_date ) = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."'
			// ) AS attendances 
			// LEFT JOIN
			// departments on departments.department_name = attendances.department
			// WHERE employee_id like 'PI%'
			// ORDER BY
			// attend_date DESC,
			// NAME ASC";

			// $attendance_lists = db::select($query); 

			$response = array(
				'status' => true,
				'attendance_lists' => $attendance_lists,
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
