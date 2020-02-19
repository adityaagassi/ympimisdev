<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\WeldingReworkLog;
use App\CodeGenerator;
use App\WeldingNgLog;
use App\WeldingCheckLog;
use App\WeldingTempLog;
use App\WeldingLog;
use App\Employee;

class WeldingProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
		$this->location_sx = [
			'phs-visual-sx',
			'hsa-sx',
			'hsa-visual-sx',
			'hsa-dimensi-sx',
			'hts-stamp-sx'
		];
		$this->hpl = [
			'ASKEY',
			'TSKEY',
			'FLKEY',
			'CLKEY'
		];
		$this->fy = db::table('weekly_calendars')->select('fiscal_year')->distinct()->get();
	}

	public function indexWeldingAchievement(){
		$title = 'Welding Group Achievement';
		$title_jp= 'HSAサックス寸法検査';

		return view('processes.welding.display.welding_group_achievement', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Welding Group Achievement')->with('head', 'Welding Process');
	}

	public function indexWeldingFL(){
		return view('processes.welding.index_fl')->with('page', 'Welding Process FL');
	}

	public function indexWeldingKensa($id){
		$ng_lists = DB::table('ng_lists')->where('location', '=', $id)->where('remark', '=', 'welding')->get();

		if($id == 'hsa-visual-sx'){
			$title = 'HSA Kensa Visual Saxophone';
			$title_jp= 'HSAサックス外観検査';
		}

		if($id == 'phs-visual-sx'){
			$title = 'PHS Kensa Visual Saxophone';
			$title_jp= 'HSAサックス外観検査';
		}

		if($id == 'hsa-dimensi-sx'){
			$title = 'HSA Kensa Dimensi Saxophone';
			$title_jp= 'HSAサックス寸法検査';
		}

		return view('processes.welding.kensa', array(
			'ng_lists' => $ng_lists,
			'loc' => $id,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Process Welding SX')->with('head', 'Welding Process');
	}

	public function indexWeldingResume($id){
		if($id == 'phs-visual-sx'){
			$title = 'PHS Saxophone NG Report';
			$title_jp = '??';
		}
		if($id == 'hsa-visual-sx'){
			$title = 'HSA Saxophone NG Report';
			$title_jp = '??';
		}
		if($id == 'hsa-dimensi-sx'){
			$title = 'Dimensi Saxophone NG Report';
			$title_jp = '??';
		}
		$fys = $this->fy;

		return view('processes.welding.report.resume', array(
			'loc' => $id,
			'fys' => $fys,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Process Welding SX')->with('head', 'Welding Process');
	}

	public function indexDisplayProductionResult(){
		$locations = $this->location_sx;

		return view('processes.welding.display.production_result', array(
			'title' => 'Welding Production Result',
			'title_jp' => '溶接生産高',
			'locations' => $locations
		))->with('page', 'Production Result');
	}

	public function indexReportNG(){
		$title = 'Not Good Record';
		$title_jp = '不良内容';
		$locations = $this->location_sx;

		return view('processes.welding.report.not_good', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('head', 'Welding Process');
	}

	public function indexReportHourly(){
		$locations = $this->location_sx;

		return view('processes.welding.report.hourly_report', array(
			'title' => 'Hourly Report',
			'title_jp' => '',
			'locations' => $locations
		))->with('page', 'Hourly Report');
	}

	public function indexNgRate(){
		$locations = $this->location_sx;

		return view('processes.welding.display.ng_rate', array(
			'title' => 'NG Rate',
			'title_jp' => '不良率',
			'locations' => $locations
		))->with('page', 'Welding Process');
	}

	public function indexOpRate(){
		$title = 'NG Rate by Operator';
		$title_jp = '作業者不良率';

		$locations = $this->location_sx;	

		return view('processes.welding.display.op_rate', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('page', 'Welding Process');
	}

	public function indexOpAnalysis(){
		$title = 'Welding OP Analysis';
		$title_jp = '溶接作業者の分析';

		$locations = $this->location_sx;

		return view('processes.welding.display.op_analysis', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('page', 'Welding OP Analysis');
	}

	public function indexWeldingOpEff(){
		return view('processes.welding.display.welding_op_eff', array(
			'title' => 'Operator Overall Efficiency',
			'title_jp' => '作業者全体能率',
		))->with('page', 'Operator Overall Efficiency');
	}

	public function indexProductionResult(){
		$locations = $this->location_sx;

		return view('processes.welding.report.production_result', array(
			'title' => 'Production Result',
			'title_jp' => '生産実績',
			'locations' => $locations
		))->with('page', 'Welding Process');		
	}

	public function fetchProductionResult(Request $request){
		$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
		$date_to = date('Y-m-d', strtotime($request->get('dateto')));

		$kensas = ['hsa-visual-sx', 'hsa-dimensi-sx', 'phs-visual-sx'];

		if($request->get('location') == 'hts-stamp-sx'){
			$results = db::table('log_processes')
			->where('log_processes.origin_group_code', '=', '043')
			->where('log_processes.process_code', '=', '1')
			->where('log_processes.created_at', '>=', $date_from)
			->where('log_processes.created_at', '<=', $date_to)
			->leftJoin('users', 'users.id', '=', 'log_processes.created_by')
			->selectRaw('users.username as employee_id, users.name, log_processes.serial_number as tag, "-" as material_number, "-" as material_description, "-" as `key`, log_processes.model, "-" as surface, log_processes.quantity, "hts-stamp-sx" as location, log_processes.created_at')
			->get();
		}
		else if(in_array($request->get('location'), $kensas)){
			$results = WeldingLog::where('welding_logs.location', '=', $request->get('location'))
			->where('welding_logs.created_at', '>=', $date_from)
			->where('welding_logs.created_at', '<=', $date_to)
			->leftJoin('users', 'users.username', '=', 'welding_logs.employee_id')
			->leftJoin('materials', 'materials.material_number', '=', 'welding_logs.material_number')
			->select('welding_logs.employee_id', 'users.name', 'welding_logs.tag', 'welding_logs.material_number', 'materials.material_description', 'materials.key', 'materials.model', 'materials.surface', 'welding_logs.quantity', 'welding_logs.location', 'welding_logs.created_at')
			->get();
		}else if($request->get('location') == 'hsa-sx'){
			$results = db::connection('welding')->select("select op.operator_nik as employee_id, op.operator_name as `name`, p.perolehan_eff as tag, hsa.hsa_kito_code as material_number, m.material_description, m.`key`, m.model, m.surface, p.perolehan_jumlah as quantity, if(p.part_type = '2', 'HSA', '') as location, p.tanggaljam as created_at from t_perolehan p
				left join m_hsa hsa on p.part_id = hsa.hsa_id
				left join m_operator op on op.operator_id = p.operator_id
				left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
				where p.part_type = '2'
				and p.flow_id = '1'
				and date(p.tanggaljam) between '".$date_from."' and '".$date_to."'
				order by p.tanggaljam asc");
		}

		return DataTables::of($results)
		->make(true);
	}

	public function fetchNgRate(Request $request){
		$now = date('Y-m-d');

		$ngs = WeldingNgLog::leftJoin('materials', 'materials.material_number', '=', 'welding_ng_logs.material_number')
		->orderBy('welding_ng_logs.created_at', 'asc');
		$checks = WeldingCheckLog::leftJoin('materials', 'materials.material_number', '=', 'welding_check_logs.material_number')
		->orderBy('welding_check_logs.created_at', 'asc');
		$addlocation = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and location in (".$location.") ";
		}

		// if(strlen($request->get('location'))>0){
		// 	$location = explode(",", $request->get('location'));
		// 	$ngs = $ngs->whereIn('welding_ng_logs.location', $location);
		// 	$checks = $checks->whereIn('welding_check_logs.location', $location);
		// }

		if(strlen($request->get('tanggal'))>0){
			$now = date('Y-m-d', strtotime($request->get('tanggal')));
			$ngs = $ngs->whereRaw('date(welding_ng_logs.created_at) = "'.$now.'"');
			$checks = $checks->whereRaw('date(welding_check_logs.created_at) = "'.$now.'"');
		}

		$ng = db::select("select SUM(quantity) as jumlah,ng_name,SUM(quantity) / (select SUM(welding_check_logs.quantity) as total_check from welding_check_logs where deleted_at is null ".$addlocation." and DATE(welding_check_logs.created_at)='".$now."') * 100 as rate from welding_ng_logs where date(created_at) = '".$now."' ".$addlocation." group by ng_name order by jumlah desc");

		$ngkey = db::select("
			select rate.`key`, rate.`check`, rate.ng, rate.rate from (
			select c.`key`, c.jml as `check`, COALESCE(ng.jml,0) as ng,(COALESCE(ng.jml,0)/c.jml*100) as rate 
			from 
			(select mt.`key`, sum(w.quantity) as jml from welding_check_logs w
			left join materials mt on mt.material_number = w.material_number
			where w.deleted_at is null
			".$addlocation."
			and DATE(w.created_at)='".$now."'
			GROUP BY mt.`key`) c

			left join

			(select mt.`key`, sum(w.quantity) as jml from welding_ng_logs w
			left join materials mt on mt.material_number = w.material_number
			where w.deleted_at is null
			".$addlocation."
			and DATE(w.created_at)='".$now."'
			GROUP BY mt.`key`) ng

			on c.`key` = ng.`key`) rate
			where rate.ng != '0'
			ORDER BY rate.rate desc"
		);


		$dateTitle = date("d M Y", strtotime($now));

		$ngs = $ngs->get();
		$checks = $checks->get();


		$datastat = db::select("select 
			COALESCE(SUM(welding_check_logs.quantity),0) as total_check,
			COALESCE((SELECT sum(quantity) from welding_logs where deleted_at is null ".$addlocation." and DATE(welding_logs.created_at)='".$now."'),0) as total_ok,

			COALESCE((select sum(quantity) from welding_ng_logs where deleted_at is null ".$addlocation." and DATE(welding_ng_logs.created_at)='".$now."'),0) as total_ng,

			COALESCE((select sum(quantity) from welding_ng_logs where deleted_at is null ".$addlocation." and DATE(welding_ng_logs.created_at)='".$now."')
			/ 
			(Select SUM(quantity) from welding_check_logs where deleted_at is null ".$addlocation." and DATE(welding_check_logs.created_at)='".$now."') * 100,0) as ng_rate 

			from welding_check_logs 
			where DATE(welding_check_logs.created_at)='".$now."' ".$addlocation." and deleted_at is null ");

		$location = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			for($x = 0; $x < count($locations); $x++) {
				$location = $location." ".$locations[$x]." ";
				if($x != count($locations)-1){
					$location = $location."&";
				}
			}
		}else{
			$location = "";
		}
		$location = strtoupper($location);
		
		$response = array(
			'status' => true,
			'checks' => $checks,
			'ngs' => $ngs,
			'ng' => $ng,
			'ngkey' => $ngkey,
			'dateTitle' => $dateTitle,
			'data' => $datastat,
			'title' => $location
		);
		return Response::json($response);
	}

	public function fetchOpRate(Request $request){
		$now = date('Y-m-d');

		$addlocation = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and location in (".$location.") ";
		}

		if(strlen($request->get('tanggal'))>0){
			$now = date('Y-m-d', strtotime($request->get('tanggal')));
		}

		$ng_target = db::table("middle_targets")->where('location', '=', 'wld')->where('target_name', '=', 'NG Rate')->select('target')->first();

		$ng_rate = db::select("select eg.`group` as shift, eg.employee_id as operator_id, e.name, rate.`check`, rate.ng, rate.rate from employee_groups eg left join 
			(select c.operator_id, c.jml as `check`, COALESCE(ng.jml,0) as ng, ROUND((COALESCE(ng.jml,0)/c.jml*100),1) as rate 
			from (select w.operator_id, sum(w.quantity) as jml from welding_check_logs w
			left join materials mt on mt.material_number = w.material_number
			where w.operator_id is not null
			".$addlocation."
			and DATE(w.created_at)='".$now."'
			GROUP BY w.operator_id) c
			left join
			(select w.operator_id, sum(w.quantity) as jml from welding_ng_logs w
			left join materials mt on mt.material_number = w.material_number
			where w.operator_id is not null
			".$addlocation."
			and DATE(w.created_at)='".$now."'
			GROUP BY w.operator_id) ng
			on c.operator_id = ng.operator_id) rate
			on rate.operator_id = eg.employee_id
			left join employee_syncs e on e.employee_id = eg.employee_id
			where eg.location = 'soldering-hsa'
			ORDER BY eg.`group`, eg.employee_id asc");

		$target = db::select("select eg.`group`, eg.employee_id, e.name, ng.material_number, concat(m.model, ' ', m.`key`) as `key`, ng.ng_name, ng.quantity, ng.created_at from employee_groups eg left join 
			(select * from welding_ng_logs where deleted_at is null ".$addlocation." and remark in 
			(select remark.remark from
			(select operator_id, max(remark) as remark from welding_ng_logs where DATE(created_at) ='".$now."' ".$addlocation." group by operator_id) 
			remark)
			) ng 
			on eg.employee_id = ng.operator_id
			left join materials m on m.material_number = ng.material_number
			left join employee_syncs e on e.employee_id = eg.employee_id
			where eg.location = 'soldering-hsa'
			order by eg.`group`, eg.employee_id asc");

		$operator = db::select("select * from employee_groups where location = 'soldering-hsa' order by `group`, employee_id asc");

		$dateTitle = date("d M Y", strtotime($now));

		$location = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			for($x = 0; $x < count($locations); $x++) {
				$location = $location." ".$locations[$x]." ";
				if($x != count($locations)-1){
					$location = $location."&";
				}
			}
		}else{
			$location = "";
		}
		$location = strtoupper($location);
		
		$response = array(
			'status' => true,
			'ng_rate' => $ng_rate,
			'target' => $target,
			'operator' => $operator,
			'ng_target' => $ng_target->target,
			'dateTitle' => $dateTitle,
			'title' => $location
		);
		return Response::json($response);
	}

	public function fetchOpAnalysis(Request $request){
		$date_from = $request->get('date_from');
		$date_to = $request->get('date_to');

		if($request->get('date_to') == null){
			if($request->get('date_from') == null){
				$from = date('Y-m')."-01";
				$now = date('Y-m-d');
			}
			elseif($request->get('date_from') != null){
				$from = $request->get('date_from');
				$now = date('Y-m-d');
			}
		}
		elseif($request->get('date_to') != null){
			if($request->get('date_from') == null){
				$from = date('Y-m')."-01";
				$now = $request->get('date_to');
			}
			elseif($request->get('date_from') != null){
				$from = $request->get('date_from');
				$now = $request->get('date_to');
			}
		}

		$actual = db::connection('welding')->select("SELECT
			DATE( d.tanggaljam_shift ) AS tgl,
			SUM(
			TIMESTAMPDIFF( MINUTE, d.starttime, d.stoptime )) AS time,
			COUNT( DISTINCT operator_id ) AS op,
			ROUND(( SUM( TIMESTAMPDIFF( MINUTE, d.starttime, d.stoptime )))/ COUNT( DISTINCT operator_id ), 2 ) AS act_time,
			ROUND(( SUM( TIMESTAMPDIFF( MINUTE, d.starttime, d.stoptime ))), 2 ) AS all_time,
			( SELECT target FROM ympimis.middle_targets WHERE target_name = 'Normal Working Time' AND location = 'wld' ) AS normal_time,
			ROUND((
			SELECT
			target 
			FROM
			ympimis.middle_targets 
			WHERE
			target_name = 'Normal Working Time' 
			AND location = 'wld' 
			) - (
			SUM(
			TIMESTAMPDIFF( MINUTE, d.starttime, d.stoptime )))/ COUNT( DISTINCT operator_id ),
			2 
			) AS loss_time,
			ROUND((
			SELECT
			SUM( perolehan_jumlah * hsa_timing )/ 60 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id 
			WHERE
			DATE( tanggaljam ) = tgl 
			AND part_type = '2' 
			)/ COUNT( DISTINCT operator_id ),
			2 
			) AS std_time,
			ROUND((
			SELECT
			target 
			FROM
			ympimis.middle_targets 
			WHERE
			target_name = 'Normal Working Time' 
			AND location = 'wld' 
			) - (
			SELECT
			SUM( perolehan_jumlah * hsa_timing )/ 60 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id 
			WHERE
			DATE( tanggaljam ) = tgl 
			AND part_type = '2' 
			)/ COUNT( DISTINCT operator_id ),
			2 
			) AS loss_time_std,
			ROUND((
			SELECT
			SUM( perolehan_jumlah * hsa_timing )/ 60 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id 
			WHERE
			DATE( tanggaljam ) = tgl 
			AND part_type = '2' 
			),
			2 
			) AS all_time_std 
			FROM
			t_data_downtime d
			LEFT JOIN ympimis.weekly_calendars ON weekly_calendars.week_date = DATE_FORMAT( d.tanggaljam_shift, '%Y-%m-%d' )
			LEFT JOIN m_mesin ON m_mesin.mesin_id = d.mesin_id 
			WHERE
			DATE( d.tanggaljam_shift ) BETWEEN '".$from."' 
			AND '".$now."' 
			AND m_mesin.department_id = 2 
			AND `status` = '1' 
			AND weekly_calendars.remark <> 'H' 
			GROUP BY
			tgl");

		// $op = db::connection('welding')->select("select DATE(d.tanggaljam_shift) as tgl, SUM(durasi) as act, count(distinct id_operator) as op from t_data_downtime d where DATE_FORMAT(d.tanggaljam_shift,'%Y-%m-%d') between '".$from."' and '".$now."' and  `status` = '1' GROUP BY tgl");

		// $emp = db::select("select g.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name` from employee_groups g left join employees e on e.employee_id = g.employee_id
		// 	where g.location = 'soldering-hsa'");

		// $datastat = db::select(" ");

		
		$dateTitleNow = date("d-M-Y", strtotime($now));
		$dateTitleFrom = date("d-M-Y", strtotime($from));

		// $location = "";
		// if($request->get('location') != null) {
		// 	$locations = explode(",", $request->get('location'));
		// 	for($x = 0; $x < count($locations); $x++) {
		// 		$location = $location." ".$locations[$x]." ";
		// 		if($x != count($locations)-1){
		// 			$location = $location."&";
		// 		}
		// 	}
		// }else{
		// 	$location = "";
		// }
		// $location = strtoupper($location);
		
		$response = array(
			'status' => true,
			'actual' => $actual,
			'from' => $from,
			'now' => $now,
			'dateTitleNow' => $dateTitleNow,
			'dateTitleFrom' => $dateTitleFrom,
			// 'title' => $location
		);
		return Response::json($response);
	}


	public function fetchWeldingOpEff(Request $request){
		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$eff_target = db::table("middle_targets")->where('location', '=', 'wld')->where('target_name', '=', 'Operator Efficiency')->select('target')->first();

		$rate = db::select("select rate.shift, rate.operator_id, concat(SPLIT_STRING(e.name, ' ', 1), ' ', SPLIT_STRING(e.name, ' ', 2)) as `name`, rate.tot, rate.ng, rate.rate from
			(select c.shift, c.operator_id, c.jml as tot, COALESCE(ng.jml,0) as ng, ((c.jml-COALESCE(ng.jml,0))/c.jml) as rate from
			(select eg.`group` as shift, m.operator_id, sum(m.quantity) as jml from middle_check_logs m
			left join materials mt on mt.material_number = m.material_number
			left join employee_groups eg on eg.employee_id = m.operator_id
			where m.location = 'bff-kensa'
			and m.operator_id is not null
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '".$date."'
			GROUP BY shift, m.operator_id) c
			left join
			(select eg.`group` as shift, m.operator_id, sum(m.quantity) as jml from middle_ng_logs m
			left join materials mt on mt.material_number = m.material_number
			left join employee_groups eg on eg.employee_id = m.operator_id
			where m.location = 'bff-kensa'
			and m.operator_id is not null
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '".$date."'
			GROUP BY shift, m.operator_id) ng
			on c.shift = ng.shift and c.operator_id = ng.operator_id) rate
			left join employees e on e.employee_id = rate.operator_id
			ORDER BY shift, rate.rate desc");

		$time_eff = db::connection('digital_kanban')->select("select e.`group`, e.employee_id, dl.act, dl.std, dl.std/dl.act as eff  from employee_groups e left join
			(select l.operator_id, sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time))/60 as act, sum((l.material_qty*t.time))/60 as std from data_log l
			left join standart_times t on l.material_number = t.material_number
			where DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') = '".$date."'
			GROUP BY l.operator_id) dl on dl.operator_id = e.employee_id
			WHERE e.location = 'bff'
			ORDER BY e.`group`, e.employee_id asc;");

		$emp_name = Employee::select('employee_id', db::raw('concat(SPLIT_STRING(employees.name, " ", 1), " ", SPLIT_STRING(employees.name, " ", 2)) as name'))->get();

		$response = array(
			'status' => true,
			'date' => $date,
			'rate' => $rate,
			'time_eff' => $time_eff,
			'emp_name' => $emp_name,
			'eff_target' => $eff_target->target,
		);
		return Response::json($response);

	}

	public function fetchReportHourly(Request $request){
		$tanggal = '';
		if(strlen($request->get('date')) > 0){
			$date = date('Y-m-d', strtotime($request->get('date')));
			$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$date."' and ";
		}else{		
			$date = date('Y-m-d');
			$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$date."' and ";
		}

		$addlocation = "";
		if($request->get('location') != null) {
			$locations = $request->get('location');
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and l.location in (".$location.") ";
		}

		$key = db::select("select DISTINCT SUBSTRING(`key`, 1, 1) as kunci from materials where hpl = 'ASKEY' and issue_storage_location = 'SX21' ORDER BY `key` asc");

		$jam = [
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '00:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '01:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '01:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '03:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '03:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '05:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '05:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '07:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '07:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '09:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '09:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '11:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '11:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '14:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '14:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '16:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '16:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '18:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '18:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '20:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '20:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '22:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '22:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '23:59:59'"
		];

		$jam2 = [
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '00:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '01:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '01:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '03:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '03:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '05:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '05:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '07:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '07:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '09:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '09:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '11:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '11:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '14:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '14:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '16:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '16:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '18:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '18:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '20:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '20:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '22:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '22:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '23:59:59'"
		];

		$dataShift3 = [];
		$dataShift1 = [];
		$dataShift2 = [];

		$z3 = [];
		$z1 = [];
		$z2 = [];

		$push_data = [];
		$push_data_z = [];

		if($request->get('location') == 'hsa-sx'){			
			for ($i=0; $i <= 3 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'ASKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'TSKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift3, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.model = 'A82'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci");
				array_push($z3, $push_data_z[$i]);
			}

			for ($i=4; $i <= 7 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'ASKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'TSKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift1, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.model = 'A82'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci");
				array_push($z1, $push_data_z[$i]);
			}

			for ($i=8; $i <= 11 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'ASKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'TSKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift2, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.model = 'A82'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci");
				array_push($z2, $push_data_z[$i]);
			}
		}else{
			for ($i=0; $i <= 3 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'ASKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'TSKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift3, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number 
					where  ".$tanggal." ".$jam[$i]." and m.model = 'A82' ".$addlocation."
					GROUP BY tgl, m.model");
				array_push($z3, $push_data_z[$i]);
			}

			for ($i=4; $i <= 7 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'ASKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'TSKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift1, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number 
					where  ".$tanggal." ".$jam[$i]." and m.model = 'A82' ".$addlocation."
					GROUP BY tgl, m.model");
				array_push($z1, $push_data_z[$i]);
			}

			for ($i=8; $i <= 11 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'ASKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'TSKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift2, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number 
					where  ".$tanggal." ".$jam[$i]." and m.model = 'A82' ".$addlocation."
					GROUP BY tgl, m.model");
				array_push($z2, $push_data_z[$i]);
			}
		}



		$tanggal = substr($tanggal,40,10);

		$response = array(
			'status' => true,
			'tanggal' => $tanggal,
			'key' => $key,
			'dataShift3' => $dataShift3,
			'dataShift1' => $dataShift1,
			'dataShift2' => $dataShift2,
			'z3' => $z3, 
			'z1' => $z1, 
			'z2' => $z2, 
		);
		return Response::json($response);
	}

	public function fetchReportNG(Request $request){
		$report = WeldingNgLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'welding_ng_logs.employee_id')
		->leftJoin('materials', 'materials.material_number', '=', 'welding_ng_logs.material_number');

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$report = $report->where(db::raw('date_format(welding_ng_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$report = $report->where(db::raw('date_format(welding_ng_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		if($request->get('location') != null){
			$report = $report->whereIn('welding_ng_logs.location', $request->get('location'));
		}

		$report = $report->select('welding_ng_logs.employee_id', 'employee_syncs.name', 'welding_ng_logs.tag', 'welding_ng_logs.material_number', 'materials.material_description', 'materials.key', 'materials.model', 'materials.surface', 'welding_ng_logs.ng_name', 'welding_ng_logs.quantity', 'welding_ng_logs.location', 'welding_ng_logs.created_at')->get();

		// return Response::json($report);

		return DataTables::of($report)->make(true);
	}

	public function fetchDisplayProductionResult(Request $request){
		$tgl="";
		if(strlen($request->get('tgl')) > 0){
			$tgl = date('Y-m-d',strtotime($request->get("tgl")));
		}else{
			$tgl = date("Y-m-d");
		}
		$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$tgl."' and";

		$addlocation = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and l.location in (".$location.") ";
		}

		if($request->get('location') == 'hsa-sx'){
			$query1 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from ympimis.materials where hpl = 'ASKEY' and surface not like '%PLT%' and issue_storage_location = 'SX21' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and date(p.tanggaljam) = '".$tgl."'
			and TIME(p.tanggaljam) > '00:00:00'
			and TIME(p.tanggaljam) < '07:00:00'
			and m.hpl = 'ASKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and date(p.tanggaljam) = '".$tgl."'
			and TIME(p.tanggaljam) > '07:00:00'
			and TIME(p.tanggaljam) < '16:00:00'
			and m.hpl = 'ASKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and date(p.tanggaljam) = '".$tgl."'
			and TIME(p.tanggaljam) > '16:00:00'
			and TIME(p.tanggaljam) < '23:59:59'
			and m.hpl = 'ASKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$alto = db::select($query1);

			$query2 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from ympimis.materials where hpl = 'ASKEY' and surface not like '%PLT%' and issue_storage_location = 'SX21' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and date(p.tanggaljam) = '".$tgl."'
			and TIME(p.tanggaljam) > '00:00:00'
			and TIME(p.tanggaljam) < '07:00:00'
			and m.hpl = 'TSKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and date(p.tanggaljam) = '".$tgl."'
			and TIME(p.tanggaljam) > '07:00:00'
			and TIME(p.tanggaljam) < '16:00:00'
			and m.hpl = 'TSKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and date(p.tanggaljam) = '".$tgl."'
			and TIME(p.tanggaljam) > '16:00:00'
			and TIME(p.tanggaljam) < '23:59:59'
			and m.hpl = 'TSKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$tenor = db::select($query2);
		}
		else{
			$query1 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where hpl = 'ASKEY' and surface not like '%PLT%' and issue_storage_location = 'SX21' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE ".$tanggal." TIME(l.created_at) > '00:00:00' and TIME(l.created_at) < '07:00:00' and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE ".$tanggal." TIME(l.created_at) > '07:00:00' and TIME(l.created_at) < '16:00:00' and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE ".$tanggal." TIME(l.created_at) > '16:00:00' and TIME(l.created_at) < '23:59:59' and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$alto = db::select($query1);

			$query2 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where hpl = 'TSKEY' and surface not like '%PLT%' and issue_storage_location = 'SX21' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE ".$tanggal." TIME(l.created_at) > '00:00:00' and TIME(l.created_at) < '07:00:00' and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE ".$tanggal." TIME(l.created_at) > '07:00:00' and TIME(l.created_at) < '16:00:00' and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE ".$tanggal." TIME(l.created_at) > '16:00:00' and TIME(l.created_at) < '23:59:59' and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$tenor = db::select($query2);
		}

		$query3 = "select distinct `key` from materials where hpl = 'ASKEY' and issue_storage_location = 'SX21' order by `key`";
		$key =  db::select($query3);

		$query4 = "select distinct model from materials where hpl = 'ASKEY' and issue_storage_location = 'SX21' order by model";
		$model_alto =  db::select($query4);

		$query5 = "select distinct model from materials where hpl = 'TSKEY' and issue_storage_location = 'SX21' order by model";
		$model_tenor =  db::select($query5);

		$location = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			for($x = 0; $x < count($locations); $x++) {
				$location = $location." ".$locations[$x]." ";
				if($x != count($locations)-1){
					$location = $location."&";
				}
			}
		}else{
			$location = "";
		}
		$location = strtoupper($location);

		$response = array(
			'status' => true,
			'alto' => $alto,
			'tenor' => $tenor,
			'key' => $key,
			'model_tenor' => $model_tenor,
			'model_alto' => $model_alto,
			'title' => $location
		);
		return Response::json($response);
	}

	public function scanWeldingOperator(Request $request){
		
		$employee = db::table('employees')->where('tag', '=', $request->get('employee_id'))->first();

		if($employee == null){
			$response = array(
				'status' => false,
				'message' => 'Tag karyawan tidak ditemukan',
			);
			return Response::json($response);			
		}

		$response = array(
			'status' => true,
			'message' => 'Tag karyawan ditemukan',
			'employee' => $employee,
		);
		return Response::json($response);
	}

	public function fetchKensaResult(Request $request){

		$location = $request->get('location');
		$employee_id = $request->get('employee_id');
		$now = date('Y-m-d');
		
		$query1 = "SELECT
		sum( IF ( materials.model <> 'A82' AND materials.hpl = 'ASKEY', welding_logs.quantity, 0 ) ) AS askey,
		sum( IF ( materials.model <> 'A82' AND materials.hpl = 'TSKEY', welding_logs.quantity, 0 ) ) AS tskey,
		sum( IF ( materials.model LIKE '%82%', welding_logs.quantity, 0 ) ) AS `z` 
		FROM
		welding_logs
		LEFT JOIN materials ON materials.material_number = welding_logs.material_number 
		WHERE
		employee_id = '".$employee_id."'
		AND date(welding_logs.created_at) = '".$now."'
		AND location = '".$location."'";

		$oks = db::select($query1);

		$query2 = "SELECT
		sum( IF ( materials.model <> 'A82' AND materials.hpl = 'ASKEY', welding_ng_logs.quantity, 0 ) ) AS askey,
		sum( IF ( materials.model <> 'A82' AND materials.hpl = 'TSKEY', welding_ng_logs.quantity, 0 ) ) AS tskey,
		sum( IF ( materials.model LIKE '%82%', welding_ng_logs.quantity, 0 ) ) AS `z` 
		FROM
		welding_ng_logs
		LEFT JOIN materials ON materials.material_number = welding_ng_logs.material_number 
		WHERE
		employee_id = '".$employee_id."'
		AND date(welding_ng_logs.created_at) = '".$now."'
		AND location = '".$location."'";

		$ngs = db::select($query2);

		$response = array(
			'status' => true,
			'oks' => $oks,
			'ngs' => $ngs,
		);
		return Response::json($response);
	}

	public function fetchGroupAchievement(Request $request){
		if ($request->get('tanggal') == "") {
			$tanggal = date('Y-m-d');
		} else {
			$tanggal = date('Y-m-d',strtotime($request->get('tanggal')));
		}

		$data = db::select("select ws.ws_name, COALESCE(bff.jml,0) as bff, COALESCE(wld.jml,0) as wld from
			(select distinct ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id
			order by ws.ws_id asc) ws
			left join
			(select ws.ws_name, sum(l.quantity) as jml from middle_request_logs l
			left join
			(select hsa.hsa_kito_code as material_number, ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id) ws
			on ws.material_number = l.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$tanggal."'
			group by ws.ws_name) bff
			on ws.ws_name = bff.ws_name
			left join
			(select ws.ws_name, sum(l.quantity) as jml from welding_logs l
			left join
			(select hsa.hsa_kito_code as material_number, ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id) ws
			on ws.material_number = l.material_number
			where l.location = 'hsa-visual-sx'
			and DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$tanggal."'
			group by ws.ws_name) wld
			on ws.ws_name = wld.ws_name");


		$response = array(
			'status' => true,
			'data' => $data,
			'tanggal' => $tanggal
		);
		return Response::json($response);
	}

	public function fetchAccumulatedAchievement(Request $request){
		if ($request->get('tanggal') == "") {
			$tanggal = date('Y-m-d');
			$tahun = date('Y');
		} else {
			$tanggal = date('Y-m-d',strtotime($request->get('tanggal')));
			$tahun = date('Y',strtotime($request->get('tanggal')));
		}

		$akumulasi = db::select("select w.week_name, acc.tgl, acc.wld, acc.bff from
			(select wld.tgl, COALESCE(wld.jml,0) as wld, COALESCE(bff.jml,0) as bff from
			(select DATE_FORMAT(w.created_at,'%Y-%m-%d') as tgl, sum(w.quantity) as jml from welding_logs w
			left join materials m on m.material_number = w.material_number
			where w.location = 'hsa-visual-sx'
			and DATE_FORMAT(w.created_at,'%Y-%m-%d') in
			(select week_date from weekly_calendars
			where week_name = (select week_name from weekly_calendars where week_date = '".$tanggal."')
			and DATE_FORMAT(week_date,'%Y') = '".$tahun."')
			group by tgl) wld
			left join
			(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, sum(l.quantity) as jml from middle_request_logs l
			left join materials m on m.material_number = l.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') in
			(select week_date from weekly_calendars
			where week_name = (select week_name from weekly_calendars where week_date = '".$tanggal."')
			and DATE_FORMAT(week_date,'%Y') = '".$tahun."')
			group by tgl) bff
			on wld.tgl = bff.tgl) acc
			left join weekly_calendars w on w.week_date = acc.tgl
			order by tgl asc");

		$response = array(
			'status' => true,
			'akumulasi' => $akumulasi,
			'tanggal' => $tanggal,
		);
		return Response::json($response);
	}

	public function scanWeldingKensa(Request $request){
		$location = explode('-', $request->get('location'))[0];
		$tag = $this->dec2hex($request->get('tag'));

		if($location == 'phs'){
			$zed_material = db::connection('welding')->table('m_phs_kartu')->leftJoin('m_phs', 'm_phs_kartu.phs_id', '=', 'm_phs.phs_id')
			->where('m_phs_kartu.phs_kartu_code', '=', $tag)
			->first();

			if($zed_material == null){
				$response = array(
					'status' => false,
					'message' => 'Tag material PHS tidak ditemukan',
				);
				return Response::json($response);
			}

			$zed_operator = db::connection('welding')->table('m_phs_kartu')->leftJoin('t_order', 't_order.part_id', '=', 'm_phs_kartu.phs_id')
			->leftJoin('t_order_detail', 't_order_detail.order_id', '=', 't_order.order_id')
			->leftJoin('m_operator', 'm_operator.operator_id', '=', 't_order_detail.operator_id')
			->where('t_order.part_type', '=', '1')
			->where('t_order_detail.flow_id', '=', '1')
			->where('t_order.kanban_no', '=', $zed_material->hsa_kartu_no)
			->select('m_operator.operator_nik', 'm_operator.operator_name', 't_order.order_id', 't_order_detail.order_sedang_finish_date')
			->first();

			$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
			->where('materials.material_number', '=', $zed_material->phs_code)
			->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
			->first();
		}
		else if($location == 'hsa'){
			$zed_material = db::connection('welding')->table('m_hsa_kartu')->leftJoin('m_hsa', 'm_hsa_kartu.hsa_id', '=', 'm_hsa.hsa_id')
			->where('m_hsa_kartu.hsa_kartu_code', '=', $tag)
			->first();

			if($zed_material == null){
				$response = array(
					'status' => false,
					'message' => 'Tag material HSA tidak ditemukan',
				);
				return Response::json($response);
			}

			$zed_operator = db::connection('welding')->table('m_hsa_kartu')->leftJoin('t_order', 't_order.part_id', '=', 'm_hsa_kartu.hsa_id')
			->leftJoin('t_order_detail', 't_order_detail.order_id', '=', 't_order.order_id')
			->leftJoin('m_operator', 'm_operator.operator_id', '=', 't_order_detail.operator_id')
			->where('m_hsa_kartu.hsa_kartu_code', '=', $tag)
			->where('t_order.part_type', '=', '2')
			->where('t_order_detail.flow_id', '=', '1')
			->where('t_order.kanban_no', '=', $zed_material->hsa_kartu_no)
			// ->select('m_operator.operator_nik', 'm_operator.operator_name', 't_order.order_id', 't_order_detail.order_sedang_finish_date')
			->first();

			$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
			->where('materials.material_number', '=', $zed_material->hsa_kito_code)
			->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
			->first();
		}

		$response = array(
			'status' => true,
			'message' => 'Material ditemukan',
			'material' => $material,
			'opwelding' => $zed_operator,
			'started_at' => date('Y-m-d H:i:s'),
			'attention_point' => asset("/welding/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
			'check_point' => asset("/welding/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
		);
		return Response::json($response);
	}

	public function inputWeldingRework(Request $request){
		$welding_rework_log = new WeldingReworkLog([
			'employee_id' => $request->get('employee_id'),
			'tag' => $request->get('tag'),
			'material_number' => $request->get('material_number'),
			'quantity' => $request->get('quantity'),
			'location' => $request->get('loc'),
			'started_at' => $request->get('started_at'),
		]);

		try{
			$welding_rework_log->save();
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
			'message' => 'Waktu pengecekan material rework berhasil tercatat'
		);
		return Response::json($response);
	}

	public function inputWeldingKensa(Request $request){

		$code_generator = CodeGenerator::where('note','=','welding-kensa')->first();
		$code = $code_generator->index+1;
		$code_generator->index = $code;
		$code_generator->save();

		$tag = $this->dec2hex($request->get('tag'));

		if($request->get('ng')){
			foreach ($request->get('ng') as $ng) {
				$welding_ng_log = new WeldingNgLog([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'ng_name' => $ng[0],
					'quantity' => $ng[1],
					'location' => $request->get('loc'),
					'welding_time' => $request->get('welding_time'),
					'operator_id' => $request->get('operator_id'),
					'started_at' => $request->get('started_at'),
					'remark' => $code,
				]);

				try{
					$welding_ng_log->save();
				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}

			try{

				$welding_check_log = new WeldingCheckLog([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('cek'),
					'location' => $request->get('loc'),
					'operator_id' => $request->get('operator_id'),
					'welding_time' => $request->get('welding_time'),
				]);
				$welding_check_log->save();

				$welding_temp_log = new WeldingTempLog([
					'material_number' => $request->get('material_number'),
					'operator_id' => $request->get('operator_id'),
					'quantity' => $request->get('cek'),
					'location' => $request->get('loc'),
				]);
				$welding_temp_log->save();

				$response = array(
					'status' => true,
					'message' => 'NG has been recorded.',
				);
				return Response::json($response);
			}catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		} else {
			if($request->get('loc') == 'hsa-visual-sx'){
				try{
					$m_hsa_kartu = db::connection('welding_controller')->table('m_hsa_kartu')->where('m_hsa_kartu.hsa_kartu_code', '=', $tag)->first();

					$order_id = db::connection('welding_controller')->table('t_order')->where('part_type', '=', '2')
					->where('part_id', '=', $m_hsa_kartu->hsa_id)
					->where('t_order.kanban_no', '=', $m_hsa_kartu->hsa_kartu_no)
					->first();

					$t_order_detail = db::connection('welding_controller')->table('t_order_detail')
					->where('order_id', '=', $order_id->order_id)
					->where('flow_id', '=', '3')
					->where('order_status', '=', '1')
					->update([
						'order_sedang_start_date' => $request->get('started_at'),
						'order_sedang_finish_date' => date('Y-m-d H:i:s'),
						'order_status' => '6'
					]);

					$t_order = db::connection('welding_controller')->table('t_order')->where('part_type', '=', '2')
					->where('part_id', '=', $m_hsa_kartu->hsa_id)
					->where('t_order.kanban_no', '=', $m_hsa_kartu->hsa_kartu_no)
					->update([
						'order_status' => '5'
					]);
				}
				catch(\Exception $e){

				}
			}
			try{
				// $buffing_inventory = db::connection('digital_kanban')->table('buffing_inventories')
				// ->where('material_tag_id', '=', $request->get('tag'))
				// ->update([
				// 	'lokasi' => 'BUFFING-AFTER',
				// ]);
				$welding_log = new WeldingLog([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('quantity'),
					'location' => $request->get('loc'),
					'operator_id' => $request->get('operator_id'),
					'welding_time' => $request->get('welding_time'),
					'started_at' => $request->get('started_at'),
				]);
				$welding_log->save();

				$temp = WeldingTempLog::where('material_number','=',$request->get('material_number'))
				->where('location','=',$request->get('loc'))
				->where('operator_id','=',$request->get('operator_id'))
				->first();

				if(count($temp) > 0){
					$delete = WeldingTempLog::where('material_number','=',$request->get('material_number'))
					->where('location','=',$request->get('loc'))
					->where('operator_id','=',$request->get('operator_id'))
					->first();

					$delete->delete();
				}else{
					$welding_check_log = new WeldingCheckLog([
						'employee_id' => $request->get('employee_id'),
						'tag' => $request->get('tag'),
						'material_number' => $request->get('material_number'),
						'quantity' => $request->get('cek'),
						'location' => $request->get('loc'),
						'operator_id' => $request->get('operator_id'),
						'welding_time' => $request->get('welding_time'),
					]);
					$welding_check_log->save();
				}

				$response = array(
					'status' => true,
					'message' => 'Input material successfull.',
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

	function dec2hex($number)
	{
		$hexvalues = array('0','1','2','3','4','5','6','7',
			'8','9','A','B','C','D','E','F');
		$hexval = '';
		while($number != '0')
		{
			$hexval = $hexvalues[bcmod($number,'16')].$hexval;
			$number = bcdiv($number,'16',0);
		}
		return $hexval;
	}
}