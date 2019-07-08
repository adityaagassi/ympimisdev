<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Libraries\ActMLEasyIf;
use Response;

class TrialController extends Controller
{
	public function trial_perolehan(){
		$year = date("Y");
		$month = date("m");
		$date = date("d");
		$hour = date("H");
		$minute = date("i");
		$second = date("s");

		$query = "SELECT flo_details.material_number, sum(flo_details.quantity) as qty FROM flo_details WHERE DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') between DATE_FORMAT(NOW() ,'%Y-%m-01') AND DATE_FORMAT(NOW() ,'%Y-%m-%d') GROUP BY flo_details.material_number";

		$tanggal = $year . '-' . $month . '-' . $date;

		$flo_details = DB::select($query);

		foreach ($flo_details as $flo_detail) {
			try{
				$perolehan = DB::connection('mysql2')
				->table('perolehan')
				->where('gmc', '=', $flo_detail->material_number)
				->where('tanggal', '=', $tanggal)
				->update(['actual' => $flo_detail->qty]);
			}
			catch (QueryException $e){
				$error_code = $e->errorInfo[1];
				echo $e;
			}
		}
	}

	public function indexCensor(){
		return view('trials.censor');
	}

	public function trialCensor(){
		try{
			$plc = new ActMLEasyIf(0);
			$datas = $plc->read_data('D0', 16);

			$response = array(
				'status' => true,
				'dataCensor' => $datas,
			);
			return Response::json($response);
		}
		catch (Exception $e){
			$response = array(
				'status' => false,
			);
			return Response::json($response);
		}
	}

	public function buffingIndex()
	{
		$title = 'Buffing Work Station Control';
		$title_jp = '???';

		return view('trials.buffing_index', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Buffing')->with('head', 'Middle Process');
	}

	public function fetchBuffingQueue(){
		$work_stations = db::connection('digital_kanban')->table('dev_list')->get();

		$material_akans = array();
		$material_sedangs = array();
		$material_selesais = array();
		$employee_ids = array();
		foreach ($work_stations as $work_station) {
			if(!in_array($work_station->dev_akan_num, $material_akans)){
				array_push($material_akans, $work_station->dev_akan_num);
			}
			if(!in_array($work_station->dev_sedang_num, $material_sedangs)){
				array_push($material_sedangs, $work_station->dev_sedang_num);
			}
			if(!in_array($work_station->dev_selesai_num, $material_selesais)){
				array_push($material_selesais, $work_station->dev_selesai_num);
			}
			if(!in_array($work_station->dev_operator_id, $employee_ids)){
				array_push($employee_ids, $work_station->dev_operator_id);
			}
		}

		$akans = db::table('materials')->whereIn('materials.material_number', $material_akans)->get();
		$sedangs = db::table('materials')->whereIn('materials.material_number', $material_sedangs)->get();
		$selesais = db::table('materials')->whereIn('materials.material_number', $material_selesais)->get();
		$employees = db::table('employees')->whereIn('employees.employee_id', $employee_ids)->get();

		$response = array(
			'status' => true,
			'work_stations' => $work_stations,
			'akans' => $akans,
			'sedangs' => $sedangs,
			'selesais' => $selesais,
			'employees' => $employees,

		);
		return Response::json($response);
	}
}
