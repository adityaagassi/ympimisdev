<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class TrialController extends Controller
{

	public function trial(){

		$year = date("Y");
		$month = date("m");
		$date = date("d");
		$hour = date("H");
		$minute = date("i");
		$second = date("s");

		$query = "SELECT weekly_calendars.week_name, DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') as production_date, hpl, shipment_schedules.material_number, materials.material_description, sum(flo_details.quantity) as qty FROM flo_details LEFT JOIN weekly_calendars on weekly_calendars.week_date = DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') LEFT JOIN flos on flos.flo_number = flo_details.flo_number LEFT JOIN shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id LEFT JOIN materials on materials.material_number = shipment_schedules.material_number WHERE DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') = :pd_date GROUP BY weekly_calendars.week_name, production_date, hpl, shipment_schedules.material_number, materials.material_description";

		$tanggal = $year . '-' . $month . '-' . $date;

		$flo_details = DB::select($query, ['pd_date' => $tanggal]);

		// dd($flo_details);
		// exit;

		foreach ($flo_details as $flo_detail) {
			try{
				$perolehan = DB::connection('mysql2')
				->table('perolehan')
				->where('gmc', '=', $flo_detail->material_number)
				->where('tanggal', '=', $flo_detail->production_date)
				->update(['actual' => $flo_detail->qty]);
			}
			catch (QueryException $e){
				$error_code = $e->errorInfo[1];
				echo $e;
				}
			}
		}
	}