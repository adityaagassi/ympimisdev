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
}
