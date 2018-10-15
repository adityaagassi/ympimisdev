<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use File;
use Response;
use Illuminate\Support\Facades\Auth;

class TrialController extends Controller
{

	public function trial_index(){
		return view('trials.export');
	}

	public function trial_export(Request $request){

		$query = "select flo_details.flo_number, shipment_schedules.material_number, materials.material_description, flo_details.serial_number, flo_details.quantity, DATE_FORMAT(flo_details.created_at, '%d/%m/%Y') as production_date from flo_details left join flos on flos.flo_number = flo_details.flo_number left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id left join materials on materials.material_number = shipment_schedules.material_number where DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') = :pd_date";

		$flo_details = DB::select($query, ['pd_date' => $request->get('production_date')]);		

		if(count($flo_details)>0){
			$year = date("Y");
			$month = date("m");
			$date = date("d");
			$hour = date("H");
			$minute = date("i");
			$second = date("s");
			$filename = "production_" . $year . $month . $date . $hour . $minute . $second . ".txt";
			$text = "";
			$index = 1;
			$filepath = public_path() . "/outputs/" . $filename;

			$text .= "flo_number\t";
			$text .= "material_number\t";
			$text .= "material_description\t";
			$text .= "serial_number\t";
			$text .= "quantity\t";
			$text .= "production_date\t";
			$text .= "\r\n";

			foreach ($flo_details as $flo_detail) {
				$text .= $flo_detail->flo_number."\t";
				$text .= $flo_detail->material_number."\t";
				$text .= $flo_detail->material_description."\t";
				$text .= $flo_detail->serial_number."\t";
				$text .= $flo_detail->quantity."\t";
				$text .= $flo_detail->production_date."\t";
				if ($index < count($flo_details)) {
					$text .= "\r\n";
				}
				$index++;
			}
			File::put($filepath, $text);
			return Response::download($filepath)->deleteFileAfterSend(true);
		}
	}

	public function trial_perolehan(){

		$year = date("Y");
		$month = date("m");
		$date = date("d");
		$hour = date("H");
		$minute = date("i");
		$second = date("s");

		$query = "SELECT shipment_schedules.material_number, sum(flo_details.quantity) as qty FROM flo_details LEFT JOIN weekly_calendars on weekly_calendars.week_date = DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') LEFT JOIN flos on flos.flo_number = flo_details.flo_number LEFT JOIN shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id LEFT JOIN materials on materials.material_number = shipment_schedules.material_number WHERE DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') between DATE_FORMAT(NOW() ,'%Y-%m-01') AND DATE_FORMAT(NOW() ,'%Y-%m-%d') GROUP BY shipment_schedules.material_number";

		$tanggal = $year . '-' . $month . '-' . $date;

		$flo_details = DB::select($query, ['pd_date' => $tanggal]);

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

	public function trial_shipment(){

		$year = date("Y");
		$month = date("m");
		$date = date("d");
		$hour = date("H");
		$minute = date("i");
		$second = date("s");

		$query = "select weekly_calendars.week_name, shipment_schedules.material_number, sum(flos.actual) as qty from flos left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.st_date where weekly_calendars.week_name = (SELECT week_name from weekly_calendars where week_date = :pd_date) GROUP BY weekly_calendars.week_name, shipment_schedules.material_number";

		$tanggal = $year . '-' . $month . '-' . $date;

		$flo_details = DB::select($query, ['pd_date' => $tanggal]);

		// dd($flo_details);
		// exit;

		foreach ($flo_details as $flo_detail) {
			try{
				$perolehan = DB::connection('mysql2')
				->table('shipment_2')
				->where('gmc', '=', $flo_detail->material_number)
				->where('week', '=', $flo_detail->week_name)
				->update(['actual' => $flo_detail->qty]);
			}
			catch (QueryException $e){
				$error_code = $e->errorInfo[1];
				echo $e;
			}
		}
	}

	public function timezone(){
		echo(date("Y-m-d H:i:s"));
	}
}