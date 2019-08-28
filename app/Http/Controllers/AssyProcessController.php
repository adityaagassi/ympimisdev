<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AssyProcessController extends Controller
{
	public function indexDisplayAssy()
	{
		$title = 'SX Sub-Assy Picking Monitor';
		$title_jp = '';

		$keys = db::select("select DISTINCT `key` from materials order by `key` ASC");


		return view('displays.assys.assy_picking', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'keys' => $keys
		))->with('page', 'Assy Picking')->with('head', 'Display');
	}

	public function fetchPicking(Request $request)
	{
		$tanggal = date('Y-m-d',strtotime($request->get('tanggal')));
		$where = "";
		$where1 = "";

		if ($request->get('key') != "") {
			$keys = explode(",",$request->get('key'));
			$keylength = count($keys);
			$key = "";

			for($x = 0; $x < $keylength; $x++) {
				$key = $key."'".$keys[$x]."'";
				if($x != $keylength -1){
					$key = $key.",";
				}
			}

			$where = " AND `key` IN (".$key.")";
		}

		if ($request->get('surface') != "") {
			if ($request->get('surface') == 'PLT') {
				$where1 = " AND surface LIKE '%PLT%'";
			} else {
				$where1 = " AND surface NOT LIKE '%PLT%'";
			}
		}

		$first = date('Y-m-01',strtotime($tanggal));

		$minsatu = date('Y-m-d',strtotime('-1 day', strtotime($tanggal)));


		$picking = "select assy_schedule.material_number, model, `key`, surface, plan - COALESCE(minus,0) as total_plan, COALESCE(picking, 0) as picking from (
		select assy_picking_schedules.material_number, model, `key`, surface, sum(quantity) as plan from assy_picking_schedules 
		left join materials on materials.material_number = assy_picking_schedules.material_number
		where due_date = '".$tanggal."' ".$where." ".$where1."
		group by assy_picking_schedules.material_number, model, `key`, surface
		) as assy_schedule left join (
		select material_number, minus from
		(select transfer_material_id, SUM(IF(category = 'transfer' OR category = 'transfer_adjustment', IF(transfer_movement_type = '9I3',lot,0),0)) - SUM(IF(category = 'transfer_cancel' OR category = 'transfer_return' OR category = 'transfer_adjustment', IF(transfer_movement_type = '9I4',lot,0),0)) as minus from kitto.histories where DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN '".$first."' AND '".$minsatu."' AND transfer_material_id is not null
		group by transfer_material_id) min 
		left join kitto.materials as k_materials on k_materials.id = min.transfer_material_id) minus on assy_schedule.material_number = minus.material_number
		left join (
		select material_number, picking from
		(select transfer_material_id, SUM(IF(category = 'transfer' OR category = 'transfer_adjustment', IF(transfer_movement_type = '9I3',lot,0),0)) as picking from kitto.histories where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$tanggal."' AND transfer_material_id is not null group by transfer_material_id) pick left join kitto.materials as k_materials on k_materials.id = pick.transfer_material_id
		) picking on picking.material_number = assy_schedule.material_number
		order by assy_schedule.`key` asc, assy_schedule.model asc
		limit 25
		";

		$picking_assy = db::select($picking);

		$response = array(
			'status' => true,
			'picking' => $picking_assy,
		);
		return Response::json($response);

	}

	public function chartPicking(Request $request)
	{
		$where = "";
		$date = $request->get('tanggal');

		if ($request->get('key') != "" OR $request->get('surface') != "") {
			$where = "WHERE ";
		}

		if($request->get('key') != "") {
			$keys = explode(",",$request->get('key'));
			$keylength = count($keys);
			$key = "";

			for($x = 0; $x < $keylength; $x++) {
				$key = $key."'".$keys[$x]."'";
				if($x != $keylength -1){
					$key = $key.",";
				}
			}

			$where .= " assy_schedules.`key` IN (".$key.")";
		}

		if ($request->get('surface') != "") {
			if ($where != "WHERE ") {
				$where .= " AND ";
			}

			if ($request->get('surface') == 'PLT') {
				$where .= " assy_schedules.surface LIKE '%PLT%'";
			} else {
				$where .= " assy_schedules.surface NOT LIKE '%PLT%'";
			}
		}

		$picking = "select assy_schedules.`key`, assy_schedules.model, assy_schedules.surface, stockroom, middle, welding from
		(select sum(stockroom) stockroom, sum(middle) middle, sum(welding) welding, `key`, model, surface from 
		(select middle.material_number, sum(middle.stockroom) as stockroom, sum(middle.middle) as middle, sum(middle.welding) as welding, materials.key, materials.model, materials.surface from
		(
		select kitto.inventories.material_number, sum(lot) as stockroom, 0 as middle, 0 as welding from kitto.inventories where kitto.inventories.issue_location like 'SX51' group by kitto.inventories.material_number

		union all

		select ympimis.middle_inventories.material_number, 0 as stockroom, sum(ympimis.middle_inventories.quantity) as middle, 0 as welding from ympimis.middle_inventories group by ympimis.middle_inventories.material_number
		) as middle left join materials on materials.material_number = middle.material_number where materials.key is not null group by middle.material_number, materials.key, materials.model, materials.surface

		union all

		select kitto.inventories.material_number, 0 as stockroom, 0 as middle, sum(kitto.inventories.lot) as welding, welding.key, welding.model, welding.surface from
		(
		select distinct bom_components.material_child, parent.key, parent.model, parent.surface from
		(
		select bom_components.material_child, materials.key, materials.model, materials.surface from materials left join bom_components on bom_components.material_parent = materials.material_number where materials.hpl in ('ASKEY', 'TSKEY') and materials.key is not null and mrpc in ('S51')
		) as parent
		left join bom_components on bom_components.material_parent = parent.material_child
		) as welding
		left join kitto.inventories on kitto.inventories.material_number = welding.material_child 
		group by kitto.inventories.material_number, welding.key, welding.model, welding.surface) as semua
		group by `key`, model, surface) as final
		right join (select `key`, model, surface from (select distinct material_number from assy_picking_schedules where due_date = '".$date."') asy left join materials on materials.material_number = asy.material_number) assy_schedules on assy_schedules.`key` = final.`key` and assy_schedules.model = final.model and assy_schedules.surface = final.surface
		".$where."
		order by assy_schedules.`key` asc, assy_schedules.model asc
		limit 25
		";

		$picking_assy = db::select($picking);

		$response = array(
			'status' => true,
			'picking' => $picking_assy,
		);
		return Response::json($response);
	}
}
