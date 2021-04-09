<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Employee;
use App\EmployeeSync;
use App\OperatorLossTime;
use App\OperatorLossTimeLog;

class EfficiencyController extends Controller
{
	public function indexOperatorLossTime(){
		$title = "Operator Loss Time Record";
		$title_jp = "";

		$employees = db::select('SELECT
			employee_syncs.employee_id,
			employee_syncs.NAME,
			employee_syncs.department,
			employee_syncs.`section`,
			employee_syncs.`group` 
			FROM
			employee_syncs');

		return view('efficiency.operator_loss_time', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees
		))->with('head', 'Operator Loss Time Record')->with('page', 'Operator Loss Time');
	}

	public function scanEmployee(Request $request){

		$employee = Employee::where('tag', '=', $request->get('tag'))->first();

		$employee_sync = EmployeeSync::where('employee_id', '=', $employee->employee_id)->first();

		$operator_loss_time = OperatorLossTime::where('employee_id', '=', $employee->employee_id)->first();

		if($operator_loss_time != null){

			try{
				$operator_loss_time_log = new OperatorLossTimeLog([
					'employee_id' => $operator_loss_time->employee_id,
					'employee_name' => $operator_loss_time->employee_name,
					'cost_center' => $operator_loss_time->cost_center,
					'position' => $operator_loss_time->position,
					'division' => $operator_loss_time->division,
					'department' => $operator_loss_time->department,
					'section' => $operator_loss_time->section,
					'group' => $operator_loss_time->group,
					'sub_group' => $operator_loss_time->sub_group,
					'reason' => $operator_loss_time->reason,
					'started_at' => $operator_loss_time->created_at
				]);
				$operator_loss_time_log->save();

				$operator_loss_time->forceDelete();

				$response = array(
					'status' => true,
					'code' => 'kembali',
					'message' => 'Karyawan kembali bekerja',
					'employee' => $employee
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$response = array(
					'status' => true,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
			
		}

		$response = array(
			'status' => true,
			'code' => 'pergi',
			'message' => 'Karyawan berhasil ditemukan',
			'employee' => $employee_sync
		);
		return Response::json($response);
	}

	public function inputOperatorLossTime(Request $request){
		try{
			$operator_loss_time = new OperatorLosstime([
				'employee_id' => $request->get('employee_id'),
				'employee_name' => $request->get('employee_name'),
				'cost_center' => $request->get('cost_center'),
				'position' => $request->get('position'),
				'division' => $request->get('division'),
				'department' => $request->get('department'),
				'section' => $request->get('section'),
				'group' => $request->get('group'),
				'sub_group' => $request->get('sub_group'),
				'reason' => $request->get('reason')
			]);
			$operator_loss_time->save();			
		}
		catch(\Exception $e){
			$response = array(
				'status' => true,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'code' => 'pergi',
			'message' => 'Karyawan meninggalkan pekerjaan'
		);
		return Response::json($response);
	}

	public function fetchOperatorLossTime(){
		$operator_loss_times = OperatorLossTime::orderBy('department', 'asc')
		->orderBy('created_at', 'asc')
		->get();

		$response = array(
			'status' => true,
			'operator_loss_times' => $operator_loss_times
		);
		return Response::json($response);
	}

	public function fetchOperatorLossTimeLog(Request $request){
		$operator_loss_time_logs = OperatorLossTimeLog::orderBy('employee_id', 'asc');

		if(strlen($request->get('record_from')) > 0){
			$record_from = date('Y-m-d', strtotime($request->get('record_from')));
			$operator_loss_time_logs = $operator_loss_time_logs->where(DB::raw('date(started_at)'), '>=', $record_from);
		}
		if(strlen($request->get('record_to')) > 0){
			$record_to = date('Y-m-d', strtotime($request->get('record_to')));
			$operator_loss_time_logs = $operator_loss_time_logs->where(DB::raw('date(started_at)'), '<=', $record_to);
		}
		if($request->get('record_employee_id') != null){
			$operator_loss_time_logs = $operator_loss_time_logs->whereIn('employee_id', $request->get('record_employee_id'));
		}
		if($request->get('record_department') != null){
			$operator_loss_time_logs = $operator_loss_time_logs->whereIn('department', $request->get('record_department'));
		}
		if($request->get('record_section') != null){
			$operator_loss_time_logs = $operator_loss_time_logs->whereIn('section', $request->get('record_section'));
		}
		if($request->get('record_group') != null){
			$operator_loss_time_logs = $operator_loss_time_logs->whereIn('group', $request->get('record_group'));
		}

		$operator_loss_time_logs = $operator_loss_time_logs->select('employee_id', 'employee_name', 'department', 'section', 'group', 'sub_group', 'reason', 'started_at', 'created_at', db::raw('timestampdiff(minute, started_at, created_at) as duration'))
		->get();

		$response = array(
			'status' => true,
			'operator_loss_time_logs' => $operator_loss_time_logs
		);
		return Response::json($response);
	}
}
