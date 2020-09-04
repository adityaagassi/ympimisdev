<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Response;
use App\AssyPickingSchedule;
use App\OriginGroup;
use App\Material;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AssyProcessController extends Controller
{
	public function indexDisplayAssy($id)
	{	
		if($id == 'assy_sax' || $id == 'welding_sax'){
			$title = 'Saxophone Picking Monitor';
			$title_jp = 'サックスのピッキング監視';

			$keys = db::select("select DISTINCT `key` from materials order by `key` ASC");
			$models = db::select("select DISTINCT model from materials where mrpc='S51' order by model ASC");
			$surfaces = array
			(
				array("","All"),
				array("LCQ","Lacquering"),
				array("PLT","Plating"),
				array("W","Washed")
			);

			$hpls = array('All', 'ASKEY', 'TSKEY');

			return view('displays.assys.assy_picking_sax', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'keys' => $keys,
				'models' => $models,
				'surfaces' => $surfaces,
				'hpls' => $hpls,
				'option' => $id
			))->with('page', 'Assy Schedule')->with('head', '');

		}elseif ($id == 'assy_cl' || $id == 'welding_cl'){
			$title = 'Clarinet Picking Monitor';
			$title_jp = '??';

			return view('displays.assys.assy_picking_cl', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'option' => $id
			))->with('page', 'Assy Schedule')->with('head', '');

		}elseif ($id == 'assy_fl') {
			$title = 'Flute Picking Monitor';
			$title_jp = '??';
		}

	}

	public function indexSchedule()
	{
		$title = 'Saxophone Picking Monitor';
		$title_jp = 'サックスのピッキング監視';

		$materials = Material::orderBy('material_number', 'ASC')->get();

		$origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();

		return view('assy_schedules.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'materials' => $materials,
			'origin_groups' => $origin_groups
		))->with('page', 'Assy Picking Schedule');
	}

	public function fetchPicking(Request $request, $id){
		$location = '';
		if($id == "assy_sax") {
			$location = 'SX51';
		}elseif ($id == "assy_cl") {
			$location = 'CL51';
		}elseif ($id == "assy_fl") {
			$location = 'FL51';
		}


		if ($request->get('tanggal') == "") {
			$tanggal = date('Y-m-d');
		} else {
			$tanggal = date('Y-m-d',strtotime($request->get('tanggal')));
		}

		$where = "";
		$where2 = "";
		$where3 = "";
		$where4 = "";
		$minus = "0";

		if (date('d', strtotime($tanggal)) != "01") {
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

			$where = " WHERE materials.`key` IN (".$key.")";
		}

		if ($request->get('model') != "") {
			if ($where != "") {
				$where2 = "AND ";
			} else {
				$where2 = "WHERE ";
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

			$where2 .= " materials.model IN (".$model.")";
		}

		if ($request->get('surface') != "") {
			if ($where != "" OR $where2 != "") {
				$where3 = "AND ";
			} else {
				$where3 = "WHERE ";
			}
			$surface = str_replace(",", "|", $request->get('surface'));			

			$where3 .= " materials.surface REGEXP '".$surface."'";
		}

		if ($request->get('hpl') != "" OR $request->get('hpl') != "All") {
			if ($where != "" OR $where2 != "" OR $where3 != "") {
				$where4 = "AND ";
			} else {
				$where4 = "WHERE ";
			}

			$hpl = str_replace(",", "|", $request->get('hpl'));			

			$where4 .= " materials.hpl REGEXP '".$hpl."'";
		}

		if ($where == "" AND $where2 == "" AND $where3 == ""  AND $where4 == "") {
			$dd = "where";
		} else {
			$dd = "and";
		}

		$first = date('Y-m-01',strtotime($tanggal));

		if (substr($tanggal, -2) != "01") {
			$minsatu = date('Y-m-d',strtotime('-1 day', strtotime($tanggal)));
		} else {
			$minsatu = date('Y-m-d');
		}

		$table = "select materials.model, materials.`key`, materials.surface , sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori, (sum(plan)-sum(picking)) as diff, sum(stock) - (sum(plan)-sum(picking)) as diff2, round(sum(stock) / sum(plan), 1) as ava from
		(
		select material_number, sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori from
		(
		select material_number, plan, picking, plus, minus, stock, plan_ori from
		(
		select materials.material_number, 0 as plan, sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', -(histories.lot),0))) as picking, 0 as plus, 0 as minus, 0 as stock, 0 as plan_ori from
		(
		select materials.id, materials.material_number from kitto.materials where materials.location = '".$location."' and category = 'key'
		) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) = '".$tanggal."' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number ) as pick

		union all

		select inventories.material_number, 0 as plan, 0 as picking, 0 as plus, 0 as minus, sum(inventories.lot) as stock, 0 as plan_ori from kitto.inventories left join kitto.materials on materials.material_number = inventories.material_number where materials.location = '".$location."' and materials.category = 'key' group by inventories.material_number

		union all

		select material_number, sum(plan) as plan, 0 as picking ,0 as plus, 0 as minus, 0 as stock, sum(plan_ori) as plan_ori from
		(
		select materials.material_number, -(sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', -(histories.lot),0)))) as plan, 0 as plan_ori from
		(
		select materials.id, materials.material_number from kitto.materials where materials.location = '".$location."' and category = 'key'
		) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '".$first."' and date(histories.created_at) <= '".$minsatu."' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number

		union all

		select assy_picking_schedules.material_number, sum(quantity) as plan, sum(quantity) as plan_ori from assy_picking_schedules 
		left join materials on materials.material_number = assy_picking_schedules.material_number
		where due_date >= '".$first."' and due_date <= '".$tanggal."'
		and assy_picking_schedules.remark = '".$location."'
		group by assy_picking_schedules.material_number
		) as plan group by material_number

		union all
		
		select materials.material_number, 0 as plan, 0 as picking, sum(if(histories.transfer_movement_type = '9I3', histories.lot,0)) as plus, sum( if(histories.transfer_movement_type = '9I4', histories.lot,0)) as minus, 0 as stock, 0 as plan_ori from
		(
		select materials.id, materials.material_number from kitto.materials where materials.location = '".$location."' and category = 'key'
		) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '".$first."' and date(histories.created_at) <= '".$tanggal."' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number
		) as final group by material_number having plan_ori > 0  
		) as final2
		join materials on final2.material_number = materials.material_number
		".$where." ".$where2." ".$where3." ".$where4."
		group by materials.model, materials.`key`, materials.surface
		order by diff desc";

		$picking_assy = db::select($table);

		$tabellength = count($picking_assy);
		$gmc = "";

		for($x = 0; $x < $tabellength; $x++) {
			$gmc = $gmc."'".$picking_assy[$x]->key.$picking_assy[$x]->model.$picking_assy[$x]->surface."'";
			if($x != $tabellength -1){
				$gmc = $gmc.",";
			}
		}

		$picking2 = "select final2.`key`, final2.model, final2.surface, stockroom, barrel, lacquering, plating, welding from
		(select sum(stockroom) stockroom, sum(barrel) as barrel, sum(lacquering) as lacquering, sum(plating) as plating, sum(welding) welding, `key`, model, surface from 
		(select middle.material_number, sum(middle.stockroom) as stockroom, sum(middle.barrel) as barrel, sum(middle.lacquering) as lacquering, sum(middle.plating) as plating, sum(middle.welding) as welding, materials.key, materials.model, materials.surface from
		(
		select kitto.inventories.material_number, sum(lot) as stockroom, 0 as barrel, 0 as lacquering, 0 as plating, 0 as welding from kitto.inventories where kitto.inventories.issue_location like '".$location."' group by kitto.inventories.material_number

		union all

		select ympimis.middle_inventories.material_number, 0 as stockroom, sum(if(location = 'barrel',ympimis.middle_inventories.quantity, 0)) as barrel, sum(if(location LIKE 'lcq%',ympimis.middle_inventories.quantity, 0)) as lacquering, sum(if(location LIKE 'plt%',ympimis.middle_inventories.quantity, 0)) as plating, 0 as welding from ympimis.middle_inventories group by ympimis.middle_inventories.material_number) as middle left join materials on materials.material_number = middle.material_number where materials.key is not null
		group by middle.material_number, materials.key, materials.model, materials.surface
		

		union all

		select kitto.inventories.material_number, 0 as stockroom,0 as barrel, 0 as lacquering, 0 as plating, sum(kitto.inventories.lot) as welding, welding.key, welding.model, welding.surface from
		(
		select distinct bom_components.material_child, parent.key, parent.model, parent.surface from
		(
		select bom_components.material_child, materials.key, materials.model, materials.surface from materials left join bom_components on bom_components.material_parent = materials.material_number where materials.hpl in ('ASKEY', 'TSKEY') and materials.key is not null and mrpc in ('S51')
		) as parent
		left join bom_components on bom_components.material_parent = parent.material_child
		) as welding
		left join kitto.inventories on kitto.inventories.material_number = welding.material_child 
		group by kitto.inventories.material_number, welding.key, welding.model, welding.surface) as semua
		group by `key`, model, surface) as final2
		join materials on materials.`key` = final2.`key` and materials.model = final2.model and materials.surface = final2.surface
		".$where." ".$where2." ".$where3." ".$where4." ".$dd." concat(final2.`key`,final2.model,final2.surface) in (".$gmc.")
		order by field(concat(final2.`key`,final2.model,final2.surface), ".$gmc.")";

		$stok = db::select($picking2);

		$bff_q = "select model, `key`, count(`key`) as buffing from buffing_inventories 
		left join materials on buffing_inventories.material_num = materials.material_number
		where lokasi in ('BUFFING','BUFFING-KENSA')
		group by model, `key`";

		$buffing = db::connection('digital_kanban')->select($bff_q);

		$dd = [];
		$stat = 0;

		foreach ($stok as $stk) {
			$row = array();
			$row['key'] = $stk->key;
			$row['model'] = $stk->model;
			$row['stockroom'] = $stk->stockroom;
			$row['barrel'] = $stk->barrel;
			$row['lacquering'] = $stk->lacquering;
			$row['plating'] = $stk->plating;
			$row['welding'] = $stk->welding;

			foreach ($buffing as $bf) {
				if ($bf->model == $stk->model && $bf->key == $stk->key) {
					$stat = 1;
					$row['buffing'] = $bf->buffing;
				}
			}

			if ($stat == 0) {
				$row['buffing'] = 0;
			}

			$dd[] = $row;
			$stat = 0;
		}

		$response = array(
			'status' => true,
			'plan' => $picking_assy,
			'stok' => $dd,
			'gmc' => $gmc
		);
		return Response::json($response);
	}

	public function fetchPickingWelding(Request $request , $id){
		$location = '';
		if($id == "welding_sax") {
			$location = 'SX51';
		}elseif ($id == "welding_cl") {
			$location = 'CL51';
		}elseif ($id == "welding_fl") {
			$location = 'FL51';
		}

		if ($request->get('tanggal') == "") {
			$tanggal = date('Y-m-d');
		} else {
			$tanggal = date('Y-m-d',strtotime($request->get('tanggal')));
		}

		$where = "";
		$where2 = "";
		$where3 = "";
		$where4 = "";
		$minus = "0";

		if (date('d', strtotime($tanggal)) != "01") {
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

			$where = " WHERE materials.`key` IN (".$key.")";
		}

		if ($request->get('model') != "") {
			if ($where != "") {
				$where2 = "AND ";
			} else {
				$where2 = "WHERE ";
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

			$where2 .= " materials.model IN (".$model.")";
		}

		if ($request->get('hpl') != "" OR $request->get('hpl') != "All") {
			if ($where != "" OR $where2 != "" OR $where3 != "") {
				$where4 = "AND ";
			} else {
				$where4 = "WHERE ";
			}

			$hpl = str_replace(",", "|", $request->get('hpl'));			

			$where4 .= " materials.hpl REGEXP '".$hpl."'";
		}

		if ($where == "" AND $where2 == ""  AND $where4 == "") {
			$dd = "where";
		} else {
			$dd = "and";
		}

		$first = date('Y-m-01',strtotime($tanggal));

		$minsatu = date('Y-m-d',strtotime('-1 day', strtotime($tanggal)));

		$table = "select materials.model, materials.`key`, sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori, (sum(plan)-sum(picking)) as diff, sum(stock) - (sum(plan)-sum(picking)) as diff2, round(sum(stock) / sum(plan), 1) as ava from
		(
		select material_number, sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori from
		(
		select material_number, plan, picking, plus, minus, stock, plan_ori from
		(
		select materials.material_number, 0 as plan, sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', -(histories.lot),0))) as picking, 0 as plus, 0 as minus, 0 as stock, 0 as plan_ori from
		(
		select materials.id, materials.material_number from kitto.materials where materials.location = '".$location."' and category = 'key'
		) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) = '".$tanggal."' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number ) as pick

		union all

		select inventories.material_number, 0 as plan, 0 as picking, 0 as plus, 0 as minus, sum(inventories.lot) as stock, 0 as plan_ori from kitto.inventories left join kitto.materials on materials.material_number = inventories.material_number where materials.location = '".$location."' and materials.category = 'key' group by inventories.material_number

		union all

		select material_number, sum(plan) as plan, 0 as picking ,0 as plus, 0 as minus, 0 as stock, sum(plan_ori) as plan_ori from
		(
		select materials.material_number, -(sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', -(histories.lot),0)))) as plan, 0 as plan_ori from
		(
		select materials.id, materials.material_number from kitto.materials where materials.location = '".$location."' and category = 'key'
		) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '".$first."' and date(histories.created_at) <= '".$minsatu."' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number

		union all

		select assy_picking_schedules.material_number, sum(quantity) as plan, sum(quantity) as plan_ori from assy_picking_schedules 
		left join materials on materials.material_number = assy_picking_schedules.material_number
		where due_date >= '".$first."' and due_date <= '".$tanggal."'
		and assy_picking_schedules.remark = '".$location."'
		group by assy_picking_schedules.material_number
		) as plan group by material_number

		union all
		
		select materials.material_number, 0 as plan, 0 as picking, sum(if(histories.transfer_movement_type = '9I3', histories.lot,0)) as plus, sum( if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 4, 0, histories.lot),0)) as minus, 0 as stock, 0 as plan_ori from
		(
		select materials.id, materials.material_number from kitto.materials where materials.location = '".$location."' and category = 'key'
		) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '".$first."' and date(histories.created_at) <= '".$tanggal."' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number
		) as final group by material_number having plan > 0  
		) as final2
		join materials on final2.material_number = materials.material_number
		".$where." ".$where2." ".$where3." ".$where4."
		group by materials.model, materials.`key`
		order by diff desc";

		$picking_assy = db::select($table);

		$tabellength = count($picking_assy);
		$gmc = "";

		for($x = 0; $x < $tabellength; $x++) {
			$gmc = $gmc."'".$picking_assy[$x]->key.$picking_assy[$x]->model."'";
			if($x != $tabellength -1){
				$gmc = $gmc.",";
			}
		}

		$picking2 = "select final2.`key`, final2.model, max(stockroom) stockroom, max(barrel) barrel, max(lacquering) lacquering, max(plating) plating, max(welding) welding from
		(select sum(stockroom) stockroom, sum(barrel) barrel, sum(lacquering) lacquering, sum(plating) plating, sum(welding) welding, `key`, model from 
		(select middle.material_number, sum(middle.stockroom) as stockroom, sum(middle.barrel) as barrel, sum(middle.lacquering) as lacquering, sum(middle.plating) as plating, sum(middle.welding) as welding, materials.key, materials.model from
		(
		select kitto.inventories.material_number, sum(lot) as stockroom, 0 as barrel, 0 as lacquering, 0 as plating, 0 as welding from kitto.inventories where kitto.inventories.issue_location like '".$location."' group by kitto.inventories.material_number

		union all

		select ympimis.middle_inventories.material_number, 0 as stockroom, sum(if(location = 'barrel',ympimis.middle_inventories.quantity, 0)) as barrel, sum(if(location LIKE 'lcq%',ympimis.middle_inventories.quantity, 0)) as lacquering, sum(if(location LIKE 'plt%',ympimis.middle_inventories.quantity, 0)) as plating, 0 as welding from ympimis.middle_inventories group by ympimis.middle_inventories.material_number
		) as middle left join materials on materials.material_number = middle.material_number where materials.key is not null
		group by middle.material_number, materials.key, materials.model
		

		union all

		select kitto.inventories.material_number, 0 as stockroom, 0 as barrel, 0 as lacquering, 0 as plating, sum(kitto.inventories.lot) as welding, welding.key, welding.model from
		(
		select distinct bom_components.material_child, parent.key, parent.model from
		(
		select bom_components.material_child, materials.key, materials.model from materials left join bom_components on bom_components.material_parent = materials.material_number where materials.hpl in ('ASKEY', 'TSKEY') and materials.key is not null and mrpc in ('S51')
		) as parent
		left join bom_components on bom_components.material_parent = parent.material_child
		) as welding
		left join kitto.inventories on kitto.inventories.material_number = welding.material_child 
		group by kitto.inventories.material_number, welding.key, welding.model) as semua
		group by `key`, model) as final2
		join materials on materials.`key` = final2.`key` and materials.model = final2.model
		".$where." ".$where2." ".$where4." ".$dd." concat(final2.`key`,final2.model) in (".$gmc.")
		group by final2.`key`, final2.model
		order by field(concat(final2.`key`,final2.model), ".$gmc.")";

		$stok = db::select($picking2);

		$bff_q = "select model, `key`, count(`key`) as buffing from buffing_inventories 
		left join materials on buffing_inventories.material_num = materials.material_number
		where lokasi in ('BUFFING','BUFFING-KENSA')
		group by model, `key`";

		$buffing = db::connection('digital_kanban')->select($bff_q);

		$dd = [];
		$stat = 0;

		foreach ($stok as $stk) {
			$row = array();
			$row['key'] = $stk->key;
			$row['model'] = $stk->model;
			$row['stockroom'] = $stk->stockroom;
			$row['barrel'] = $stk->barrel;
			$row['lacquering'] = $stk->lacquering;
			$row['plating'] = $stk->plating;
			$row['welding'] = $stk->welding;

			foreach ($buffing as $bf) {
				if ($bf->model == $stk->model && $bf->key == $stk->key) {
					$stat = 1;
					$row['buffing'] = $bf->buffing;
				}
			}

			if ($stat == 0) {
				$row['buffing'] = 0;
			}

			$dd[] = $row;
			$stat = 0;
		}

		$response = array(
			'status' => true,
			'plan' => $picking_assy,
			// 'stok' => $stok,
			// 'buffing' => $buffing,
			'stok' => $dd,
			'gmc' => $gmc
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

		if ($request->get('key') != "" OR $request->get('surface') != "" OR $request->get('model') OR $request->get('hpl') != "") {
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

		if ($request->get('hpl') != "") {
			if ($where != "WHERE ") {
				$where .= " AND ";
			}

			$where .= " assy_schedules.hpl = '".$request->get('hpl')."'";

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
		right join (select `key`, model, surface, hpl from (select distinct material_number from assy_picking_schedules where due_date BETWEEN '".$first."' AND '".$date."') asy left join materials on materials.material_number = asy.material_number) assy_schedules on assy_schedules.`key` = final.`key` and assy_schedules.model = final.model and assy_schedules.surface = final.surface
		".$where."
		";

		$picking_assy = db::select($picking);

		$response = array(
			'status' => true,
			'picking' => $picking_assy,
			'order' => $order
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

	public function fetchSchedule()
	{
		$assy_schedules = AssyPickingSchedule::leftJoin("materials","materials.material_number","=","assy_picking_schedules.material_number")
		->leftJoin("origin_groups","origin_groups.origin_group_code","=","materials.origin_group_code")
		->select('assy_picking_schedules.id','assy_picking_schedules.material_number','assy_picking_schedules.due_date','assy_picking_schedules.quantity','materials.material_description','origin_groups.origin_group_name')
		->orderByRaw('due_date DESC', 'assy_picking_schedules.material_number ASC')
		->get();

		return DataTables::of($assy_schedules)
		->addColumn('action', function($assy_schedules){
			return '
			<button class="btn btn-xs btn-info" data-toggle="tooltip" title="Delete" onclick="modalView('.$assy_schedules->id.')">View</button>
			<button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Delete" onclick="modalEdit('.$assy_schedules->id.')">Edit</button>
			<button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$assy_schedules->id.')">Delete</button>';
		})
		->rawColumns(['action' => 'action'])
		->make(true);
	}

	public function import(Request $request)
	{
		try{
			if($request->hasFile('assy_schedule')){
				$id = Auth::id();

				$file = $request->file('assy_schedule');
				$data = file_get_contents($file);

				$rows = explode("\r\n", $data);

				$date = date('Y-m', strtotime(str_replace('/','-', explode("\t",$rows[0])[1])));

				$delete_assy = AssyPickingSchedule::where(db::raw('date_format(assy_picking_schedules.due_date,"%Y-%m")'), '=', $date)->forceDelete();

				foreach ($rows as $row)
				{
					if (strlen($row) > 0) {
						$row = explode("\t", $row);
						$assy_schedule = new AssyPickingSchedule([
							'material_number' => $row[0],
							'due_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[1]))),
							'quantity' => $row[2],
							'created_by' => $id,
						]);

						$assy_schedule->save();
					}
				}
				return redirect('/index/assy_schedule')->with('status', 'New assy schedule has been imported.')->with('page', 'Assy Schedule');
			}
			else
			{
				return redirect('/index/assy_schedule')->with('error', 'Please select a file.')->with('page', 'Assy Schedule');
			}
		}

		catch (QueryException $e){
			$error_code = $e->errorInfo[1];
			if($error_code == 1062){
				return back()->with('error', 'Assy schedule with preferred due date already exist.')->with('page', 'Assy Schedule');
			}
			else{
				return back()->with('error', $e->getMessage())->with('page', 'Assy Schedule');
			}

		}
	}

	public function createSchedule(Request $request)
	{
		$due_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('due_date'))));

		try
		{
			$id = Auth::id();
			$assy_schedule = new AssyPickingSchedule([
				'material_number' => $request->get('material_number'),
				'due_date' => $due_date,
				'quantity' => $request->get('quantity'),
				'created_by' => $id
			]);

			$assy_schedule->save();  

			$response = array(
				'status' => true
			);
			return Response::json($response);
		}
		catch (QueryException $e){
			$error_code = $e->errorInfo[1];
			if($error_code == 1062){
				$response = array(
					'status' => false,
					'Message'=> 'already exist'
				);
				return Response::json($response);
			}
			else{
				$response = array(
					'status' => false
				);
				return Response::json($response);
			}
		}
	}

	public function delete(Request $request)
	{
		$assy_schedule = AssyPickingSchedule::where('id', '=', $request->get("id"))
		->forceDelete();

		$response = array(
			'status' => true
		);

		return Response::json($response);
	}

	public function edit(Request $request)
	{
		$head = AssyPickingSchedule::where('id', '=', $request->get('id'))
		->first();

		$head->quantity = $request->get('quantity');
		$head->save();

		$response = array(
			'status' => true
		);

		return Response::json($response);
	}

	public function fetchEdit(Request $request)
	{
		$assy_schedule = AssyPickingSchedule::where('id', '=', $request->get("id"))
		->first();

		$response = array(
			'status' => true,
			'datas' => $assy_schedule
		);

		return Response::json($response);
	}


	public function destroy(Request $request)
	{
		$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
		$date_to = date('Y-m-d', strtotime($request->get('dateto')));

		$materials = Material::whereIn('origin_group_code', $request->get('origin_group'))->select('material_number')->get();

		$AssyPickingSchedule = AssyPickingSchedule::where('due_date', '>=', $date_from)
		->where('due_date', '<=', $date_to)
		->whereIn('material_number', $materials)
		->forceDelete();

		return redirect('/index/assy_schedule')
		->with('status', 'Assy schedules has been deleted.')
		->with('page', 'Assy Picking Schedule');
	}

	public function view(Request $request)
	{
		$query = "select assy.material_number, assy.due_date, assy.quantity, users.`name`, material_description, origin_group_name, assy.created_at, assy.updated_at from
		(select material_number, due_date, quantity, created_by, created_at, updated_at from assy_picking_schedules where id = ".$request->get('id').") as assy
		left join materials on materials.material_number = assy.material_number
		left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
		left join users on assy.created_by = users.id";

		$assy_schedule = DB::select($query);

		$response = array(
			'status' => true,
			'datas' => $assy_schedule
		);

		return Response::json($response);

	}
}
