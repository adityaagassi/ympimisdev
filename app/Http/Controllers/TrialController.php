<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrialController extends Controller
{

	public function trial(){

		$query = "SELECT weekly_calendars.week_name, DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') as production_date, hpl, shipment_schedules.material_number, materials.material_description, sum(flo_details.quantity) as qty FROM flo_details LEFT JOIN weekly_calendars on weekly_calendars.week_date = DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') LEFT JOIN flos on flos.flo_number = flo_details.flo_number LEFT JOIN shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id LEFT JOIN materials on materials.material_number = shipment_schedules.material_number WHERE DATE_FORMAT(flo_details.created_at, '%Y-%m-%d') = :pd_date GROUP BY weekly_calendars.week_name, production_date, hpl, shipment_schedules.material_number, materials.material_description";

		// $flo_details = DB::select($query, ['pd_date' => $request->get('production_date')]);

		$perolehan = DB::connection('ympi-produksi')->table('perolehan')->get();

		dd($perolehan);

	}
	

}
