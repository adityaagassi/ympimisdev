<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AssyProcessController extends Controller
{
	public function indexDisplayAssy()
	{
		$title = 'Saxophone Picking Monitor';
		$title_jp = 'サックスのピッキング監視';

		$keys = db::select("select DISTINCT `key` from materials order by `key` ASC");
		$models = db::select("select DISTINCT model from materials where mrpc='S51' order by model ASC");


		return view('displays.assys.assy_picking', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'keys' => $keys,
			'models' => $models
		))->with('page', 'Assy Picking')->with('head', 'Display');
	}

	public function fetchPicking(Request $request)
	{
		if ($request->get('tanggal') == "") {
			$tanggal = date('Y-m-d');
		} else {
			$tanggal = date('Y-m-d',strtotime($request->get('tanggal')));
		}

		$where = "";
		$where1 = "";
		$where2 = "";
		$minus = "0";

		if ($tanggal != "2019-08-01") {
			$minus = " COALESCE(minus,0) ";
		}

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

		if ($request->get('model') != "") {
			$models = explode(",",$request->get('model'));
			$modellength = count($models);
			$model = "";

			for($x = 0; $x < $modellength; $x++) {
				$model = $model."'".$models[$x]."'";
				if($x != $modellength -1){
					$model = $model.",";
				}
			}

			$where2 = " AND model IN (".$model.")";
		}

		if ($request->get('surface') != "") {
			if ($request->get('surface') == 'PLT') {
				$where1 = " AND surface LIKE '%PLT%'";
			} else if ($request->get('surface') == 'LCQ') {
				$where1 = " AND surface LIKE '%LCQ%'";
			} else {
				$where1 = " AND surface = 'W'";
			}
		}

		$first = date('Y-m-01',strtotime($tanggal));

		$minsatu = date('Y-m-d',strtotime('-1 day', strtotime($tanggal)));

		$picking = "select assy_schedule.material_number, model, `key`, surface, plan - ".$minus." as total_plan, COALESCE(picking, 0) as picking from (
		select assy_picking_schedules.material_number, model, `key`, surface, sum(quantity) as plan from assy_picking_schedules 
		left join materials on materials.material_number = assy_picking_schedules.material_number
		where due_date BETWEEN '".$first."' AND '".$tanggal."' ".$where." ".$where1." ".$where2."
		group by assy_picking_schedules.material_number, model, `key`, surface
	) as assy_schedule ";
	if ($tanggal != "2019-08-01") {
		$picking .= "left join (
		select material_number, minus from
		(select transfer_material_id, SUM(IF(category = 'transfer' OR category = 'transfer_adjustment', IF(transfer_movement_type = '9I3',lot,0),0)) - SUM(IF(category = 'transfer_cancel' OR category = 'transfer_return' OR category = 'transfer_adjustment', IF(transfer_movement_type = '9I4',lot,0),0)) as minus from kitto.histories where DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN '".$first."' AND '".$minsatu."' AND transfer_material_id is not null
		group by transfer_material_id) min 
		left join kitto.materials as k_materials on k_materials.id = min.transfer_material_id) minus on assy_schedule.material_number = minus.material_number ";
	}

	$picking .= "left join ( select material_number, picking from
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

	if ($request->get('tanggal') == "") {
		$date = date('Y-m-d');
	} else {
		$date = date('Y-m-d',strtotime($request->get('tanggal')));
	}

	$first = date('Y-m-01',strtotime($date));

	if ($request->get('key') != "" OR $request->get('surface') != "" OR $request->get('model')) {
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

	if ($request->get('model') != "") {
		if ($where != "WHERE ") {
			$where .= " AND ";
		}

		$models = explode(",",$request->get('model'));
		$modellength = count($models);
		$model = "";

		for($x = 0; $x < $modellength; $x++) {
			$model = $model."'".$models[$x]."'";
			if($x != $modellength -1){
				$model = $model.",";
			}
		}

		$where .= " assy_schedules.model IN (".$model.")";
	}

	if ($request->get('surface') != "") {
		if ($where != "WHERE ") {
			$where .= " AND ";
		}

		if ($request->get('surface') == 'PLT') {
			$where .= " assy_schedules.surface LIKE '%PLT%'";
		} else if ($request->get('surface') == 'LCQ') {
			$where .= " assy_schedules.surface LIKE '%LCQ%'";
		} else {
			$where .= " assy_schedules.surface = 'W'";
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
	right join (select `key`, model, surface from (select distinct material_number from assy_picking_schedules where due_date BETWEEN '".$first."' AND '".$date."') asy left join materials on materials.material_number = asy.material_number) assy_schedules on assy_schedules.`key` = final.`key` and assy_schedules.model = final.model and assy_schedules.surface = final.surface
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

public function fetchPickingDetail(Request $request)
{
	$key = $request->get("key");
	$model = $request->get("model");
	$surface = $request->get("surface");

	$loc = $request->get("location");

	if ($loc == "Welding") {
		$query = "select inventories.barcode_number as tag,inventories.material_number, inventories.description as material_description , inventories.lot as quantity from
		(select distinct ympimis.bom_components.material_child, parent.key, parent.model, parent.surface from
		(select bom_components.material_child, materials.key, materials.model, materials.surface from ympimis.materials left join ympimis.bom_components on bom_components.material_parent = ympimis.materials.material_number where materials.hpl in ('ASKEY', 'TSKEY') and materials.key is not null and mrpc in ('S51')
		) as parent
		left join ympimis.bom_components on ympimis.bom_components.material_parent = parent.material_child
		where parent.key = '".$key."' AND parent.model = '".$model."' AND parent.surface = '".$surface."'
		) as welding
		left join inventories on inventories.material_number = welding.material_child ";

} else if ($loc == "Middle") {
	$query = "select stok.tag ,stok.material_number, ympimis.materials.material_description, stok.quantity from
	(select tag, ympimis.middle_inventories.material_number, ympimis.middle_inventories.quantity from ympimis.middle_inventories) stok
	left join ympimis.materials on ympimis.materials.material_number = stok.material_number
	where ympimis.materials.key = '".$key."' AND ympimis.materials.model = '".$model."' AND ympimis.materials.surface = '".$surface."'";

} else if ($loc == "Stockroom") {
	$query = "select tag, stok.material_number, ympimis.materials.material_description, stok.quantity from
	(select inventories.barcode_number as tag, inventories.material_number, lot as quantity from inventories where inventories.issue_location like 'SX51') stok
	left join ympimis.materials on ympimis.materials.material_number = stok.material_number
	where ympimis.materials.key = '".$key."' AND ympimis.materials.model = '".$model."' AND ympimis.materials.surface = '".$surface."'";
}

$detailData = db::connection('mysql2')->select($query);

return DataTables::of($detailData)->make(true);
}
}
