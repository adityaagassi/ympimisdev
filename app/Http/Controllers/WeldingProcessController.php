<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\CodeGenerator;
use App\Material;
use App\WeldingNgLog;
use App\WeldingCheckLog;
use App\WeldingReworkLog;
use App\WeldingTempLog;
use App\WeldingLog;
use App\WeldingInventory;
use App\MaterialPlantDataList;
use App\Employee;
use App\StandardTime;
use App\Jig;
use App\JigBom;
use App\JigSchedule;
use App\JigKensaCheck;
use App\JigKensa;
use App\JigKensaLog;
use App\JigRepair;
use App\JigRepairLog;
use App\JigPartStock;
use App\SolderingStandardTime;
use App\WorkshopJobOrderLog;
use App\WorkshopJobOrder;
use Carbon\Carbon;
use DateTime;
use FTP;
use File;

use Storage;


class WeldingProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
		$this->location_sx = [
			'phs-sx',
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

	public function indexWeldingTrend(){
		$emps = db::select("select eg.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name` from employee_groups eg
			left join employees e on eg.employee_id = e.employee_id
			where eg.location = 'soldering'
			order by e.`name`");

		return view('processes.welding.display.welding_op_trend', array(
			'title' => 'Welding Operator Trends',
			'title_jp' => '',
			'emps' => $emps
		))->with('page', 'Operator Trends');
	}

	public function indexWeldingEff(){
		return view('processes.welding.display.welding_eff', array(
			'title' => 'Operator Efficiency',
			'title_jp' => '作業者能率',
		))->with('page', 'Operator Efficiency');
	}

	public function indexMasterOperator(){
		$title = 'Master Operator Welding';
		$title_jp = '溶接作業者マスター';

		$list_op = DB::SELECT("SELECT
			* 
			FROM
			`employee_syncs` 
			WHERE
			department = 'Welding-Surface Treatment (WI-WST)' 
			AND section = 'Welding Process'");

		return view('processes.welding.master_operator', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'list_op2' => $list_op,
			'list_op' => $list_op
		))->with('page', 'Master Operator');		
	}

	public function indexMasterKanban($loc){

		$list_ws = DB::connection('welding_controller')->select("SELECT * FROM m_ws");
		$materials = MaterialPlantDataList::select('material_plant_data_lists.id','material_plant_data_lists.material_number','material_plant_data_lists.material_description')
		->orderBy('material_plant_data_lists.id','ASC')
		->get();

		$materials2 = MaterialPlantDataList::select('material_plant_data_lists.id','material_plant_data_lists.material_number','material_plant_data_lists.material_description')
		->orderBy('material_plant_data_lists.id','ASC')
		->get();

		if ($loc == 'hpp-sx') {
			$title = 'HPP Saxophone Kanban Master';
			$title_jp = 'サックス圧入かんばんマスター';
			return view('processes.welding.master_kanban', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'list_ws' => $list_ws,
				'list_ws2' => $list_ws,
				'materials' => $materials,
				'materials2' => $materials2,
			))->with('page', 'HPP');
		}elseif($loc == 'phs-sx'){
			$title = 'PHS Saxophone Kanban Master';
			$title_jp = 'サックスサーブロー付けかんばんマスター';
			return view('processes.welding.master_kanban', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'list_ws' => $list_ws,
				'list_ws2' => $list_ws,
				'materials' => $materials,
				'materials2' => $materials2,
			))->with('page', 'PHS');
		}elseif($loc == 'hsa-sx'){
			$title = 'HSA Saxophone Kanban Master';
			$title_jp = '集成ロー付けかんばんマスター';
			return view('processes.welding.master_kanban', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'list_ws' => $list_ws,
				'list_ws2' => $list_ws,
				'materials' => $materials,
				'materials2' => $materials2,
			))->with('page', 'HSA');
		}
	}

	public function indexCurrentWelding(){
		$title = 'Ongoing Welding';
		$title_jp = '溶接中';

		return view('processes.welding.display.current_welding', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Current Welding');		
	}

	public function indexWeldingJig(){
		return view('processes.welding.jig.index')->with('page', 'Welding Digital Jig Handling');		
	}

	public function indexWeldingKensaJig(){
		$title = 'Welding Kensa Jig';
		$title_jp = '検査冶具溶接';

		return view('processes.welding.jig.kensa', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Kensa Jig');
	}

	public function indexWeldingRepairJig(){
		$title = 'Welding Repair Jig';
		$title_jp = '溶接冶具の修正';

		return view('processes.welding.jig.repair', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Repair Jig');
	}

	public function indexWeldingJigData(){
		$title = "Welding Jig Data";
		$title_jp = "溶接冶具のデータ";

		return view('processes.welding.jig.data', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig Data');
	}

	public function indexWeldingJigBom(){
		$title = "Welding Jig BOM";
		$title_jp = "溶接冶具のBOM";

		return view('processes.welding.jig.bom', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig BOM');
	}

	public function indexWeldingJigSchedule(){
		$title = "Welding Jig Schedule";
		$title_jp = "溶接冶具のスケジュール";

		return view('processes.welding.jig.schedule', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig Schedule');
	}

	public function indexWldJigMonitoring()
	{
		$title = 'Kensa Welding Jig Monitoring';
		$title_jp = '溶接冶具の検査の監視';

		return view('processes.welding.jig.monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Kensa Welding Jig Monitoring');
	}

	public function indexWeldingKensaPoint()
	{
		$title = 'Point Check Kensa Welding Jig';
		$title_jp = '溶接冶具の検査項目';

		$jig_parent = Jig::where('category','KENSA')->get();
		$jig_child = Jig::get();

		return view('processes.welding.jig.point', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'jig_parent' => $jig_parent,
			'jig_child' => $jig_child,
			'jig_parent2' => $jig_parent,
			'jig_child2' => $jig_child
		))->with('page', 'Point Check Kensa Welding Jig');
	}

	public function indexWeldingJigPart()
	{
		$title = 'Kensa Welding Jig Parts';
		$title_jp = '溶接冶具の部品検査';

		$jig_part = Jig::where('category','PART')->get();

		return view('processes.welding.jig.part', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'jig_part' => $jig_part,
			'jig_part2' => $jig_part,
		))->with('page', 'Kensa Welding Jig Parts');
	}

	public function indexEffHandling(){
		$title = 'Average Working Time';
		$title_jp = '作業時間の平均';
		$locations = $this->location_sx;

		return view('processes.welding.display.eff_handling', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('page', 'Welding Process');
	}

	public function indexWeldingAdjustment(){
		$title = 'Saxophone Welding Adjustment';
		$title_jp = 'サックス溶接かんばん調整';

		$workstations = db::connection('welding')->select("select distinct ws.ws_name from m_hsa hsa
			left join m_ws ws on ws.ws_id = hsa.ws_id
			order by ws.ws_id asc");

		$materials = db::connection('welding_controller')->select("select welding.id, welding.material_number, welding.type, welding.model, CONCAT(m.`key`,' ',m.model) as nickname, m.material_description from
			(select hsa_id as id, hsa_kito_code as material_number, 'HSA' as type, if(hsa_jenis = 0, 'ALTO', if(hsa_jenis = 1, 'TENOR', 'A82')) as model from m_hsa
			union
			select m_phs.phs_id as id, phs_code as material_number, if(m_phs.phs_ishpp = 1, 'HPP', 'PHS') as type, if(phs_jenis = 0, 'ALTO', if(phs_jenis = 1, 'TENOR', 'A82')) as model from m_phs
			where m_phs.phs_ishpp = 0 and m_phs.phs_code not like '%H%') welding
			left join ympimis.materials m on m.material_number = welding.material_number
			order by welding.type, nickname asc");

		return view('processes.welding.welding_adjustment', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'materials' => $materials,
			'workstations' => $workstations,
		))->with('page', 'welding-queue');
	}

	public function indexWeldingBoard($loc){

		$startA = '07:00:00';
		$finishA = '16:00:00';
		$startB = '15:55:00';
		$finishB = '00:15:00';
		$startC = '23:30:00';
		$finishC = '07:10:00';

		if ($loc == 'hpp-sx') {
			$title = 'HPP Saxophone Welding Board';
			$title_jp = 'HPP サックス溶接加工順';
			return view('processes.welding.display.welding_board_hpp', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'HPP');
		}elseif ($loc == 'cuci-solder'){
			$title = 'Cuci Asam Saxophone Welding Board';
			$title_jp = ' サックス溶接加工順';
			return view('processes.welding.display.welding_board_cuci_solder', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'CUCI SOLDER');
		}elseif($loc == 'phs-sx'){
			$title = 'PHS Saxophone Welding Board';
			$title_jp = 'PHS サックス溶接加工順';
			return view('processes.welding.display.welding_board', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'PHS');
		}elseif($loc == 'hsa-sx'){
			$title = 'HSA Saxophone Welding Board';
			$title_jp = 'HSA サックス溶接加工順';
			return view('processes.welding.display.welding_board', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'HSA');
		}
	}

	public function indexWeldingAchievement(){
		$title = 'Welding Group Achievement';
		$title_jp= 'HSAサックス寸法検査';

		$workstations = db::connection('welding')->select("select distinct ws.ws_name from m_hsa hsa
			left join m_ws ws on ws.ws_id = hsa.ws_id
			order by ws.ws_id asc");


		return view('processes.welding.display.welding_group_achievement', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'workstations' => $workstations,
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
			$title_jp = 'サックスサーブロー付け不良率のリポート';
		}
		if($id == 'hsa-visual-sx'){
			$title = 'HSA Saxophone NG Report';
			$title_jp = 'サックス集成ロー付け不良率のリポート';
		}
		if($id == 'hsa-dimensi-sx'){
			$title = 'Dimensi Saxophone NG Report';
			$title_jp = 'サックス寸法不良のリポート';
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

	public function fetchWeldingData(Request $request){
		$jigs = Jig::orderBy('jig_id', 'asc')->get();

		return DataTables::of($jigs)
		->addColumn('action', function($jigs){
			return '
			<button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$materials->id.')">View</button>
			<button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$materials->id.')">Edit</button>
			<button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$materials->id.',\''.$materials->material_number.'\')">Delete</button>';
		})
		->rawColumns(['action' => 'action'])
		->make(true);
	}

	public function fetchMasterOperator(Request $request)
	{
		$lists = DB::connection('welding_controller')->
		SELECT("SELECT
			* 
			FROM
			`m_operator`");

		$response = array(
			'status' => true,
			'lists' => $lists
		);
		return Response::json($response);
	}

	public function addOperator(Request $request)
	{
		$list_op = DB::SELECT("SELECT
			* 
			FROM
			`employee_syncs` 
			WHERE
			department = 'Welding-Surface Treatment (WI-WST)' 
			AND section = 'Welding Process' 
			AND employee_id = '".$request->get('operator')."'");

		foreach ($list_op as $key) {
			$operator_name = $key->name;
		}

		$tag = dechex($request->get('operator_code'));

		$lists = DB::connection('welding_controller')
		->table('m_operator')
		->insert([
			'operator_name' => strtoupper($operator_name),
			'operator_code' => strtoupper($tag),
			'department_id' => 0,
			'ws_id' => 0,
			'operator_nik' => $request->get('operator'),
			'group' => $request->get('group'),
			'operator_create_date' => date('Y-m-d H:i:s'),
			'created_by' => Auth::id()]);

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function destroyOperator($id)
	{
		DB::connection('welding_controller')
		->table('m_operator')
		->where('operator_id','=',$id)->delete();

		return redirect('index/welding/operator');
	}

	public function getOperator(Request $request)
	{
		$list = DB::connection('welding_controller')
		->table('m_operator')
		->where('operator_id',$request->get('id'))->get();

		$lists = array();
		foreach ($list as $key) {
			array_push($lists, [
				'operator_id' => $key->operator_id,
				'operator_code' => hexdec($key->operator_code),
				'operator_nik' => $key->operator_nik,
				'group' => $key->group,
			]);
		}

		$response = array(
			'status' => true,
			'lists' => $lists
		);
		return Response::json($response);
	}

	public function updateOperator(Request $request)
	{
		$list_op = DB::SELECT("SELECT
			* 
			FROM
			`employee_syncs` 
			WHERE
			department = 'Welding-Surface Treatment (WI-WST)' 
			AND section = 'Welding Process' 
			AND employee_id = '".$request->get('operator')."'");

		foreach ($list_op as $key) {
			$operator_name = $key->name;
		}

		// $tag = dechex($request->get('operator_code'));

		$lists = DB::connection('welding_controller')
		->table('m_operator')
		->where('operator_id',$request->get('operator_id'))
		->update([
			'operator_name' => strtoupper($operator_name),
			// 'operator_code' => strtoupper($tag),
			'department_id' => 0,
			'ws_id' => 0,
			'operator_nik' => $request->get('operator'),
			'group' => $request->get('group'),
			'operator_create_date' => date('Y-m-d H:i:s')]);

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function editKanban(Request $request){

		$gmc = $request->get('gmc');
		$ws = $request->get('ws');
		$std = $request->get('std');
		$loc = $request->get('loc');		

		try{
			if($loc == 'hsa-sx'){
				$update_ws = db::connection('welding_controller')
				->table('m_hsa')
				->where('hsa_kito_code', '=', $gmc)
				->update([
					'ws_id' => $ws
				]);

				$update_std = StandardTime::updateOrCreate(
					['material_number' => $gmc],
					['process' => 'wld-hsa',
					'location' => 'soldering',
					'time' => $std,
					'created_by' => Auth::id(),
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now()]
				);

				$update_std = SolderingStandardTime::updateOrCreate(
					['material_number' => $gmc],
					['process' => 'wld-hsa',
					'location' => 'soldering',
					'time' => $std,
					'created_by' => Auth::id(),
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now()]
				);

			}else{
				$update_ws = db::connection('welding_controller')
				->table('m_phs')
				->where('phs_code', '=', $gmc)
				->update([
					'ws_id' => $ws
				]);

				$update_std = StandardTime::updateOrCreate(
					['material_number' => $gmc],
					['process' => 'wld-phs',
					'location' => 'soldering',
					'time' => $std,
					'created_by' => Auth::id(),
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now()]
				);

				$update_std = SolderingStandardTime::updateOrCreate(
					['material_number' => $gmc],
					['process' => 'wld-phs',
					'location' => 'soldering',
					'time' => $std,
					'created_by' => Auth::id(),
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now()]
				);
			}

			$response = array(
				'status' => true
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

	public function fetchShowEdit(Request $request){
		$loc = $request->get('loc');
		if ($loc == 'hsa-sx') {
			$lists = DB::connection('welding_controller')->select('SELECT
				hsa_id AS id,
				hsa_kito_code AS gmc,
				hsa_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				hsa_qty AS qty,
				hsa_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_hsa
				LEFT JOIN m_ws ON m_ws.ws_id = m_hsa.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_hsa.hsa_kito_code
				where hsa_id = '. $request->get('id'));

		}elseif ($loc == 'phs-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				phs_id AS id,
				phs_code AS gmc,
				phs_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				phs_qty AS qty,
				phs_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_phs
				LEFT JOIN m_ws ON m_ws.ws_id = m_phs.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_phs.phs_code
				WHERE
				phs_ishpp = 0 
				AND phs_code NOT LIKE '%H%'
				AND phs_id = ". $request->get('id'));

		}elseif ($loc == 'hpp-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				phs_id AS id,
				phs_code AS gmc,
				phs_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				phs_qty AS qty,
				phs_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_phs
				LEFT JOIN m_ws ON m_ws.ws_id = m_phs.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_phs.phs_code
				WHERE
				phs_ishpp = 1
				AND phs_code NOT LIKE '%H%'
				AND phs_id = ". $request->get('id'));
		}

		$response = array(
			'status' => true,
			'lists' => $lists
		);
		return Response::json($response);
	}

	public function fetchMasterKanban(Request $request){
		$loc = $request->get('loc');
		if ($loc == 'hsa-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				hsa_id AS id,
				hsa_kito_code AS gmc,
				hsa_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				hsa_qty AS qty,
				hsa_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_hsa
				LEFT JOIN m_ws ON m_ws.ws_id = m_hsa.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_hsa.hsa_kito_code");
		}elseif ($loc == 'phs-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				phs_id AS id,
				phs_code AS gmc,
				phs_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				phs_qty AS qty,
				phs_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_phs
				LEFT JOIN m_ws ON m_ws.ws_id = m_phs.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_phs.phs_code
				WHERE
				phs_ishpp = 0 
				AND phs_code NOT LIKE '%H%'");
		}elseif ($loc == 'hpp-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				phs_id AS id,
				phs_code AS gmc,
				phs_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				phs_qty AS qty,
				phs_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_phs
				LEFT JOIN m_ws ON m_ws.ws_id = m_phs.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_phs.phs_code
				WHERE
				phs_ishpp = 1
				AND phs_code NOT LIKE '%H%'");
		}

		$response = array(
			'status' => true,
			'lists' => $lists
		);
		return Response::json($response);
	}

	public function destroyKanban($loc,$id)
	{
		if ($loc == 'hsa-sx') {
			DB::connection('welding_controller')
			->table('m_hsa')
			->where('m_hsa.hsa_id','=',$id)->delete();
		}elseif ($loc == 'phs-sx') {
			DB::connection('welding_controller')
			->table('m_phs')
			->where('m_phs.phs_id','=',$id)->delete();
		}elseif ($loc == 'hpp-sx') {
			DB::connection('welding_controller')
			->table('m_phs')
			->where('m_phs.phs_id','=',$id)->delete();
		}

		return redirect('index/welding/master_kanban/'.$loc);
	}

	public function addKanban(Request $request)
	{
		$loc = $request->get('loc');
		$materials = DB::SELECT("SELECT * FROM material_plant_data_lists where material_number = '".$request->get('material_number')."'");

		foreach ($materials as $key) {
			$material_description = $key->material_description;
		}

		if ($loc == 'hsa-sx') {
			$lists = DB::connection('welding_controller')
			->table('m_hsa')
			->insert([
				'product_id' => 1,
				'hsa_name' => $material_description,
				'hsa_kito_code' => $request->get('material_number'),
				'hsa_type' => 2,
				'ws_id' => $request->get('ws'),
				'hsa_barcode' => "",
				'hsa_ket' => "hsa",
				'hsa_qty' => $request->get('qty'),
				'hsa_jenis' => $request->get('jenis'),
				'hsa_timing' => $request->get('std_time'),
				'hsa_pre' => 0,
			]);
		}else if ($loc == 'phs-sx') {
			$lists = DB::connection('welding_controller')
			->table('m_phs')
			->insert([
				'hsa_id' => 1,
				'product_id' => 1,
				'phs_name' => $material_description,
				'phs_desc' => $material_description,
				'phs_code' => $request->get('material_number'),
				'phs_type' => 2,
				'ws_id' => $request->get('ws'),
				'phs_barcode' => "",
				'phs_ket' => "phs",
				'phs_qty' => $request->get('qty'),
				'phs_jenis' => $request->get('jenis'),
				'phs_timing' => $request->get('std_time'),
				'phs_pre' => 1,
				'phs_no' => 1,
				'phs_ishpp' => 0,
			]);
		}else if ($loc == 'hpp-sx') {
			$lists = DB::connection('welding_controller')
			->table('m_phs')
			->insert([
				'hsa_id' => 1,
				'product_id' => 1,
				'phs_name' => $material_description,
				'phs_desc' => $material_description,
				'phs_code' => $request->get('material_number'),
				'phs_type' => 2,
				'ws_id' => $request->get('ws'),
				'phs_barcode' => "",
				'phs_ket' => "phs",
				'phs_qty' => $request->get('qty'),
				'phs_jenis' => $request->get('jenis'),
				'phs_timing' => $request->get('std_time'),
				'phs_pre' => 1,
				'phs_no' => 1,
				'phs_ishpp' => 1,
			]);
		}

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function fetchCurrentwelding(){
		$current = db::connection('welding_controller')->select("SELECT mesin.mesin_id, mesin.ws_id, ws.ws_name, mesin.mesin_nama, mesin.operator_nik, e.`name`, datas.part_type, datas.material_number, m.model,m.`key`, datas.sedang, ceil( s.time * v.lot_completion / 60 ) AS std FROM
			(SELECT * FROM m_mesin m WHERE m.mesin_nama like '%Sol#%') AS mesin
			LEFT JOIN
			(SELECT m.mesin_id, m.ws_id, m.mesin_nama, 'PHS' AS part_type, op.operator_nik, m.order_id_sedang_gmc AS material_number, p.proses_sedang_start_date AS sedang FROM m_mesin m
			LEFT JOIN t_proses p ON p.proses_id = m.order_id_sedang
			LEFT JOIN m_operator op ON op.operator_id = m.operator_id
			WHERE	m.flow_id = 1
			AND p.part_type = 1
			UNION
			SELECT m.mesin_id, m.ws_id, m.mesin_nama, 'HSA' AS part_type, op.operator_nik, m.order_id_sedang_gmc AS material_number, p.proses_sedang_start_date AS sedang FROM m_mesin m
			LEFT JOIN t_proses p ON p.proses_id = m.order_id_sedang
			LEFT JOIN m_operator op ON op.operator_id = m.operator_id
			WHERE m.flow_id = 1 
			AND p.part_type = 2) AS datas
			ON mesin.mesin_id = datas.mesin_id
			LEFT JOIN m_ws ws ON ws.ws_id = mesin.ws_id
			LEFT JOIN ympimis.materials m ON m.material_number = datas.material_number
			LEFT JOIN ympimis.employee_syncs e ON e.employee_id = mesin.operator_nik
			LEFT JOIN ympimis.standard_times s ON s.material_number = datas.material_number
			LEFT JOIN ympimis.material_volumes v ON v.material_number = datas.material_number 
			ORDER BY mesin.ws_id ASC");

		$response = array(
			'status' => true,
			'current' => $current,
		);
		return Response::json($response);
	}

	public function fetchWeldingBoard(Request $request){
		$loc = $request->get('loc');
		$boards = array();
		// $list_antrian = array();
		if ($loc == 'hsa-sx') {
			$work_stations = DB::connection('welding_controller')->select("SELECT
				m_mesin.mesin_nama,
				m_ws.ws_name,
				m_mesin.operator_id,
				m_mesin.operator_name,
				m_mesin.operator_nik,
				m_operator.`group` AS shift,
				TIME( m_mesin.operator_change_date ) AS jam_shift,
				m_sedang.surface AS surface_sedang,
				order_id_sedang_gmc AS gmcsedang,
				CONCAT( m_sedang.model, ' ', m_sedang.`key` ) AS sedang_name,
				order_id_sedang_name AS gmcdescsedang,
				order_id_sedang_date AS waktu_sedang,
				m_akan.surface AS surface_akan,
				order_id_akan_gmc AS gmcakan,
				CONCAT( m_akan.model, ' ', m_akan.`key` ) AS akan_name,
				order_id_akan_name AS gmcdescakan,
				order_id_akan_date AS waktu_akan 
				FROM
				m_mesin
				LEFT JOIN m_ws ON m_ws.ws_id = m_mesin.ws_id
				LEFT JOIN m_operator ON m_operator.operator_id = m_mesin.operator_id
				LEFT JOIN ympimis.materials m_akan ON m_akan.material_number = m_mesin.order_id_akan_gmc
				LEFT JOIN ympimis.materials m_sedang ON m_sedang.material_number = m_mesin.order_id_sedang_gmc 
				WHERE
				m_mesin.mesin_type = 2 
				ORDER BY
				m_ws.ws_id");
		}elseif ($loc == 'phs-sx') {
			$work_stations = DB::connection('welding_controller')->select("SELECT
				m_mesin.mesin_nama,
				m_ws.ws_name,
				m_mesin.operator_id,
				m_mesin.operator_name,
				m_mesin.operator_nik,
				m_operator.`group` AS shift,
				TIME( m_mesin.operator_change_date ) AS jam_shift,
				m_sedang.surface AS surface_sedang,
				order_id_sedang_gmc AS gmcsedang,
				CONCAT( m_sedang.model, ' ', m_sedang.`key` ) AS sedang_name,
				order_id_sedang_name AS gmcdescsedang,
				order_id_sedang_date AS waktu_sedang,
				m_akan.surface AS surface_akan,
				order_id_akan_gmc AS gmcakan,
				CONCAT( m_akan.model, ' ', m_akan.`key` ) AS akan_name,
				order_id_akan_name AS gmcdescakan,
				order_id_akan_date AS waktu_akan
				FROM
				m_mesin
				LEFT JOIN m_ws ON m_ws.ws_id = m_mesin.ws_id
				LEFT JOIN m_operator ON m_operator.operator_id = m_mesin.operator_id
				LEFT JOIN ympimis.materials m_akan ON m_akan.material_number = m_mesin.order_id_akan_gmc
				LEFT JOIN ympimis.materials m_sedang ON m_sedang.material_number = m_mesin.order_id_sedang_gmc
				WHERE
				( m_mesin.mesin_type = 1 AND m_mesin.mesin_nama LIKE '%Sol#%' ) 
				OR ( m_mesin.mesin_type = 3 AND m_mesin.mesin_nama LIKE '%Sol#%' ) 
				ORDER BY
				m_ws.ws_id");
		}elseif ($loc == 'hpp-sx') {
			$work_stations = DB::connection('welding_controller')->select("SELECT
				m_mesin.mesin_nama,
				m_ws.ws_name,
				m_mesin.operator_id,
				m_mesin.operator_name,
				m_mesin.operator_nik,
				m_operator.`group` AS shift,
				TIME( m_mesin.operator_change_date ) AS jam_shift,
				m_sedang.surface AS surface_sedang,
				order_id_sedang_gmc AS gmcsedang,
				CONCAT( m_sedang.model, ' ', m_sedang.`key` ) AS sedang_name,
				order_id_sedang_name AS gmcdescsedang,
				order_id_sedang_date AS waktu_sedang,
				m_akan.surface AS surface_akan,
				order_id_akan_gmc AS gmcakan,
				CONCAT( m_akan.model, ' ', m_akan.`key` ) AS akan_name,
				order_id_akan_name AS gmcdescakan,
				order_id_akan_date AS waktu_akan 
				FROM
				m_mesin
				LEFT JOIN m_ws ON m_ws.ws_id = m_mesin.ws_id
				LEFT JOIN m_operator ON m_operator.operator_id = m_mesin.operator_id
				LEFT JOIN ympimis.materials m_akan ON m_akan.material_number = m_mesin.order_id_akan_gmc
				LEFT JOIN ympimis.materials m_sedang ON m_sedang.material_number = m_mesin.order_id_sedang_gmc 
				WHERE
				m_mesin.mesin_type = 1 
				AND m_mesin.department_id = 4 
				ORDER BY
				m_ws.ws_id");
		}elseif ($loc == 'cuci-solder') {
			$work_stations = DB::connection('welding_controller')->select("SELECT
				a.date as store_date,
				TIME(a.datetime) as store_time,
				a.gmc,
				a.material_description as gmcdesc, 
				'0000-00-00 00:00:00' as waktu_akan,
				'0000-00-00 00:00:00' as waktu_sedang
				FROM
				(
				SELECT
				DATE( order_store_date ) AS date,
				order_store_date AS datetime,
				hsa_name AS material_description,
				hsa_kito_code AS gmc 
				FROM
				`t_before_cuci`
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_before_cuci.part_id 
				WHERE
				order_status = 0 
				AND part_type = 2 UNION ALL
				SELECT
				DATE( order_store_date ) AS date,
				order_store_date AS datetime,
				phs_name AS material_description,
				phs_code AS gmc 
				FROM
				`t_before_cuci`
				LEFT JOIN m_phs ON m_phs.phs_id = t_before_cuci.part_id 
				WHERE
				order_status = 0 
				AND part_type = 1 
			) a");
		}

		$indexCuci1 = 0;

		foreach ($work_stations as $ws) {
			$dt_now = new DateTime();

			$dt_akan = new DateTime($ws->waktu_akan);
			$akan_time = $dt_akan->diff($dt_now);

			$dt_sedang = new DateTime($ws->waktu_sedang);
			$sedang_time = $dt_sedang->diff($dt_now);

			$lists = '';
			$list_antrian = array();

			if ($loc == 'hsa-sx') {
				
				$lists = DB::connection('welding_controller')->select("SELECT
					queue.*,
					CONCAT( m.model, ' ', m.`key` ) AS `name`,
					if(queue.part_type = 1, 'PHS', 'HSA') as type
					FROM
					(
					SELECT
					proses_id,
					part_id,
					COALESCE ( m_hsa.hsa_kito_code, m_phs.phs_code ) AS hsa_kito_code,
					COALESCE ( m_hsa.hsa_name, m_phs.phs_name ) AS hsa_name,
					COALESCE ( ws_phs.ws_name, ws_hsa.ws_name ) AS ws_name,
					t_proses.part_type,
					antrian_date
					FROM
					t_proses
					LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
					LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
					LEFT JOIN m_ws AS ws_phs ON m_phs.ws_id = ws_phs.ws_id
					LEFT JOIN m_ws AS ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
					WHERE
					( t_proses.proses_status = 0 AND t_proses.part_type = 1 AND ws_phs.ws_name = '".$ws->ws_name."' ) 
					OR ( t_proses.proses_status = 0 AND t_proses.part_type = 2 AND ws_hsa.ws_name = '".$ws->ws_name."' ) 
					ORDER BY
					antrian_date ASC
					) queue
					LEFT JOIN ympimis.materials m ON m.material_number = queue.hsa_kito_code
					ORDER BY
					antrian_date ASC ");

				if (count($lists) > 9) {
					foreach ($lists as $key) {
						if (isset($key)) {
							$gmcdesc = explode(' ', $key->hsa_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							array_push($list_antrian, '('.$key->type.')'.'<br>'.$key->hsa_kito_code.'<br>'.$key->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}else{
					for ($i=0; $i < 10; $i++) {
						if (isset($lists[$i])) {
							$gmcdesc = explode(' ', $lists[$i]->hsa_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							array_push($list_antrian, '('.$lists[$i]->type.')'.'<br>'.$lists[$i]->hsa_kito_code.'<br>'.$lists[$i]->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}
			}elseif ($loc == 'phs-sx') {
				$lists = DB::connection('welding_controller')->select("SELECT
					queue.*,
					CONCAT( m.model, ' ', m.`key` ) AS `name`,
					if(queue.part_type = 1, 'PHS', 'HSA') as type
					FROM
					(
					SELECT
					proses_id,
					part_id,
					COALESCE ( m_hsa.hsa_kito_code, m_phs.phs_code ) AS phs_code,
					COALESCE ( m_hsa.hsa_name, m_phs.phs_name ) AS phs_name,
					COALESCE ( ws_phs.ws_name, ws_hsa.ws_name ) AS ws_name,
					t_proses.part_type,
					antrian_date
					FROM
					t_proses
					LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
					LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
					LEFT JOIN m_ws AS ws_phs ON m_phs.ws_id = ws_phs.ws_id
					LEFT JOIN m_ws AS ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
					WHERE
					( t_proses.proses_status = 0 AND t_proses.part_type = 1 AND ws_phs.ws_name = '".$ws->ws_name."' ) 
					OR ( t_proses.proses_status = 0 AND t_proses.part_type = 2 AND ws_hsa.ws_name = '".$ws->ws_name."' ) 
					ORDER BY
					antrian_date ASC 
					) queue
					LEFT JOIN ympimis.materials m ON m.material_number = queue.phs_code
					ORDER BY
					antrian_date ASC ");
				
				if (count($lists) > 9) {
					foreach ($lists as $key) {
						if (isset($key)) {
							$gmcdesc = explode(' ', $key->phs_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							array_push($list_antrian, '('.$key->type.')'.'<br>'.$key->phs_code.'<br>'.$key->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}else{
					for ($i=0; $i < 10; $i++) {
						if (isset($lists[$i])) {
							$gmcdesc = explode(' ', $lists[$i]->phs_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							array_push($list_antrian, '('.$lists[$i]->type.')'.'<br>'.$lists[$i]->phs_code.'<br>'.$lists[$i]->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}
			}elseif ($loc == 'hpp-sx') {
				$lists = DB::connection('welding_controller')->select("SELECT
					queue.*,
					IF(queue.phs_jenis = 0,CONCAT( 'Alto ', m.model, ' ', m.`key` ),IF(queue.phs_jenis = 1,CONCAT( 'Tenor ', m.model, ' ', m.`key`),CONCAT( 'A82Z ', m.model, ' ', m.`key` )) ) AS `name`
					FROM
					(
					SELECT
					proses_id,
					part_id,
					COALESCE ( m_hsa.hsa_kito_code, m_phs.phs_code ) AS phs_code,
					COALESCE ( m_hsa.hsa_name, m_phs.phs_name ) AS phs_name,
					COALESCE ( ws_phs.ws_name, ws_hsa.ws_name ) AS ws_name,
					COALESCE ( m_hsa.hsa_jenis, m_phs.phs_jenis ) AS phs_jenis,
					antrian_date
					FROM
					t_proses
					LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
					LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
					LEFT JOIN m_ws AS ws_phs ON m_phs.ws_id = ws_phs.ws_id
					LEFT JOIN m_ws AS ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
					WHERE
					( t_proses.proses_status = 0 AND t_proses.part_type = 1 AND ws_phs.ws_name = '".$ws->ws_name."' ) 
					OR ( t_proses.proses_status = 0 AND t_proses.part_type = 2 AND ws_hsa.ws_name = '".$ws->ws_name."' ) 
					ORDER BY
					antrian_date ASC 
					) queue
					LEFT JOIN ympimis.materials m ON m.material_number = queue.phs_code
					ORDER BY
					antrian_date ASC ");

				if (count($lists) > 9) {
					foreach ($lists as $key) {
						if (isset($key)) {
							$gmcdesc = explode(' ', $key->phs_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							// $hsaname = explode(' ', $key->phs_name);
							// array_push($list_antrian, $key->phs_code.'<br>'.$key->phs_name);
							array_push($list_antrian, $key->phs_code.'<br>'.$key->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}else{
					for ($i=0; $i < 10; $i++) {
						if (isset($lists[$i])) {
							$gmcdesc = explode(' ', $lists[$i]->phs_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							// $hsaname = explode(' ', $lists[$i]->phs_name);
							// array_push($list_antrian, $lists[$i]->phs_code.'<br>'.$lists[$i]->phs_name);
							array_push($list_antrian, $lists[$i]->phs_code.'<br>'.$lists[$i]->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}
			}

			if ($loc != 'cuci-solder') {
				$gmcdescsedang = explode(' ', $ws->gmcdescsedang);
				if (ISSET($gmcdescsedang[1])) {
					$descsedang = $gmcdescsedang[0].' '.$gmcdescsedang[1];
				}else{
					$descsedang = $gmcdescsedang[0];
				}

				$gmcdescakan = explode(' ', $ws->gmcdescakan);
				if (ISSET($gmcdescakan[1])) {
					$descakan = $gmcdescakan[0].' '.$gmcdescakan[1];
				}else{
					$descakan = $gmcdescakan[0];
				}

				$board_sedang = '';
				if($ws->surface_sedang != null){

					if($ws->surface_sedang == 'HPP') {
						$board_sedang = $ws->gmcsedang.'<br>'.$ws->sedang_name;
					}else{
						$board_sedang = '('.$ws->surface_sedang.')'.'<br>'.$ws->gmcsedang.'<br>'.$ws->sedang_name;
					}
				}else{
					$board_sedang = '<br>';
				}
				$board_akan = '';		
				if($ws->surface_akan != null){
					$board_akan = '('.$ws->surface_akan.')'.'<br>'.$ws->gmcakan.'<br>'.$ws->akan_name;
				}else{
					$board_akan = '<br>';
				}

				array_push($boards, [
					'ws_name' => $ws->ws_name,
					'mesin_name' => $ws->mesin_nama,
					'ws' => $ws->mesin_nama.'<br>'.$ws->ws_name,
					'employee_id' => $ws->operator_nik,
					'employee_name' => $ws->operator_name,
					'shift' => $ws->shift,
					'jam_shift' => $ws->jam_shift,
					'sedang' => $board_sedang,
					'akan' => $board_akan,
					'akan_time' => $akan_time->format('%H:%i:%s'),
					'sedang_time' => $sedang_time->format('%H:%i:%s'),
					'queue_1' => $list_antrian[0],
					'queue_2' => $list_antrian[1],
					'queue_3' => $list_antrian[2],
					'queue_4' => $list_antrian[3],
					'queue_5' => $list_antrian[4],
					'queue_6' => $list_antrian[5],
					'queue_7' => $list_antrian[6],
					'queue_8' => $list_antrian[7],
					'queue_9' => $list_antrian[8],
					'queue_10' => $list_antrian[9],
					'jumlah_urutan' => count($lists)
				]);
			}else{
				$gmcdesc = explode(' ', $ws->gmcdesc);
				if (ISSET($gmcdesc[1])) {
					$desc = $gmcdesc[0].' '.$gmcdesc[1];
				}else{
					$desc = $gmcdesc[0];
				}
				array_push($boards, [
					'queue' => $ws->gmc.'<br>'.$desc.'<br>'.$ws->store_date.'<br>'.$ws->store_time
				]);
				$indexCuci1++;
			}
		}

		$response = array(
			'status' => true,
			'loc' => $loc,
			'boards' => $boards,
		);
		return Response::json($response);
	}

	public function fetchDetailWeldingBoard(Request $request)
	{
		$loc = $request->get('loc');
		$ws_name = $request->get('ws_name');
		$list_antrian = array();
		if ($loc == 'hsa-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				t_proses.proses_id,
				t_proses.part_id,
				COALESCE(m_hsa.hsa_kito_code,m_phs.phs_code) as gmc,
				COALESCE(m_hsa.hsa_name,m_phs.phs_name) as gmcdesc,
				COALESCE(ws_phs.ws_name ,ws_hsa.ws_name) as ws_name
				FROM
				t_proses
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
				LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
				LEFT JOIN m_ws as ws_phs ON m_phs.ws_id = ws_phs.ws_id 
				LEFT JOIN m_ws as ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
				WHERE
				(t_proses.proses_status = 0
				AND t_proses.part_type = 1 
				AND ws_phs.ws_name = '".$ws_name."' )
				OR
				(t_proses.proses_status = 0 
				AND t_proses.part_type = 2
				AND ws_hsa.ws_name = '".$ws_name."' )
				ORDER BY
				pesanan_id,proses_id ASC");
			
		}elseif ($loc == 'phs-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				t_proses.proses_id,
				t_proses.part_id,
				COALESCE(m_hsa.hsa_kito_code,m_phs.phs_code) as gmc,
				COALESCE(m_hsa.hsa_name,m_phs.phs_name) as gmcdesc,
				COALESCE(ws_phs.ws_name ,ws_hsa.ws_name) as ws_name
				FROM
				t_proses
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
				LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
				LEFT JOIN m_ws as ws_phs ON m_phs.ws_id = ws_phs.ws_id 
				LEFT JOIN m_ws as ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
				WHERE
				(t_proses.proses_status = 0
				AND t_proses.part_type = 1 
				AND ws_phs.ws_name = '".$ws_name."' )
				OR
				(t_proses.proses_status = 0 
				AND t_proses.part_type = 2
				AND ws_hsa.ws_name = '".$ws_name."' )
				ORDER BY
				pesanan_id,proses_id ASC");
		}elseif ($loc == 'hpp-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				t_proses.proses_id,
				t_proses.part_id,
				COALESCE(m_hsa.hsa_kito_code,m_phs.phs_code) as gmc,
				COALESCE(m_hsa.hsa_name,m_phs.phs_name) as gmcdesc,
				COALESCE(ws_phs.ws_name ,ws_hsa.ws_name) as ws_name
				FROM
				t_proses
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
				LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
				LEFT JOIN m_ws as ws_phs ON m_phs.ws_id = ws_phs.ws_id 
				LEFT JOIN m_ws as ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
				WHERE
				(t_proses.proses_status = 0
				AND t_proses.part_type = 1 
				AND ws_phs.ws_name = '".$ws_name."' )
				OR
				(t_proses.proses_status = 0 
				AND t_proses.part_type = 2
				AND ws_hsa.ws_name = '".$ws_name."' )
				ORDER BY
				pesanan_id,proses_id ASC");

		}

		foreach ($lists as $key) {
			array_push($list_antrian, [
				'ws_name' => $key->ws_name,
				'gmc' => $key->gmc,
				'gmcdesc' => $key->gmcdesc,
			]);
		}

		$response = array(
			'status' => true,
			'loc' => $loc,
			'ws_name' => $ws_name,
			'list_antrian' => $list_antrian,
		);
		return Response::json($response);
	}

	public function fetchEffHandling(Request $request){
		$date = '';
		$location = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		if($request->get('location') == 'phs-sx'){
			$time = db::select("select material.material_number, material.hpl, result.phs_name as `key`, material.model, result.operator_nik, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, result.actual, result.std, (result.actual-result.std) as diff from 
				(SELECT phs.phs_code, phs.phs_name, op.operator_nik, ceil(avg(timestampdiff(second,p.perolehan_start_date,p.perolehan_finish_date))) as actual, avg(p.perolehan_jumlah * std.time) as std  FROM soldering_db.t_perolehan p
				left join soldering_db.m_phs phs on phs.phs_id = p.part_id
				left join standard_times std on std.material_number = phs.phs_code
				left join soldering_db.m_operator op on op.operator_id = p.operator_id
				where p.flow_id = '1'
				and p.part_type = '1'
				and date(perolehan_finish_date) = '".$date."'
				and p.operator_id <> 0
				group by phs.phs_code, phs.phs_name, op.operator_nik) result
				left join
				(select * from materials
				where mrpc = 's11' and surface like '%PHS%') material
				on material.material_number = result.phs_code
				left join employee_syncs e on e.employee_id = result.operator_nik
				where result.actual > 0
				order by diff desc, material.hpl, result.phs_name, material.model asc");

			$location = "PHS-SAX";

		}else if($request->get('location') == 'hsa-sx'){
			$time = db::select("select material.material_number, material.hpl, material.`key`, material.model, result.operator_nik, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, result.actual, result.std, (result.actual-result.std) as diff from
				(SELECT hsa.hsa_kito_code, op.operator_nik, ceil(avg(timestampdiff(second,p.perolehan_start_date,p.perolehan_finish_date))) as actual, avg(p.perolehan_jumlah * std.time) as std  FROM soldering_db.t_perolehan p
				left join soldering_db.m_hsa hsa on hsa.hsa_id = p.part_id
				left join standard_times std on std.material_number = hsa.hsa_kito_code
				left join soldering_db.m_operator op on op.operator_id = p.operator_id
				where p.flow_id = '1'
				and p.part_type = '2'
				and date(perolehan_finish_date) = '".$date."'
				and p.operator_id <> 0
				group by hsa.hsa_kito_code, op.operator_nik) result
				left join
				(select * from materials m
				where m.mrpc = 's21' and m.hpl like '%KEY%') material
				on material.material_number = result.hsa_kito_code
				left join employee_syncs e on e.employee_id = result.operator_nik
				where result.actual > 0
				order by diff desc, material.hpl, material.`key`, material.model asc");

			$location = "HSA-SAX";

		}else{
			$time = db::select("select material.material_number, material.hpl, material.`key`, material.model, result.operator_nik, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, result.actual, result.std, (result.actual-result.std) as diff from
				(SELECT hsa.hsa_kito_code, op.operator_nik, ceil(avg(timestampdiff(second,p.perolehan_start_date,p.perolehan_finish_date))) as actual, avg(p.perolehan_jumlah * std.time) as std  FROM soldering_db.t_perolehan p
				left join soldering_db.m_hsa hsa on hsa.hsa_id = p.part_id
				left join standard_times std on std.material_number = hsa.hsa_kito_code
				left join soldering_db.m_operator op on op.operator_id = p.operator_id
				where p.flow_id = '1'
				and p.part_type = '2'
				and date(perolehan_finish_date) = '".$date."'
				and p.operator_id <> 0
				group by hsa.hsa_kito_code, op.operator_nik) result
				left join
				(select * from materials m
				where m.mrpc = 's21' and m.hpl like '%KEY%') material
				on material.material_number = result.hsa_kito_code
				left join employee_syncs e on e.employee_id = result.operator_nik
				where result.actual > 0
				order by diff desc, material.hpl, material.`key`, material.model asc");

			$location = "HSA-SAX";
		}
		
		$response = array(
			'status' => true,
			'date' => $date,
			'time' => $time,
			'location' => $location,
		);
		return Response::json($response);

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
		}else if($request->get('location') == 'phs-sx'){
			$results = db::connection('welding')->select("select op.operator_nik as employee_id, op.operator_name as `name`, p.perolehan_eff as tag, phs.phs_code as material_number, m.material_description, m.`key`, m.model, m.surface, p.perolehan_jumlah as quantity, if(p.part_type = '1', 'PHS', '') as location, p.tanggaljam as created_at from t_perolehan p
				left join m_phs phs on p.part_id = phs.phs_id
				left join m_operator op on op.operator_id = p.operator_id
				left join ympimis.materials m on m.material_number = phs.phs_code
				where p.part_type = '1'
				and p.flow_id = '1'
				and date(p.tanggaljam) between '".$date_from."' and '".$date_to."'
				order by p.tanggaljam asc");
		}

		return DataTables::of($results)
		->make(true);
	}

	public function fetchWeldingResume(Request $request){

		$loc = $request->get('loc');
		
		$bulan = date('m-Y');
		if(strlen($request->get('bulan')) > 0){
			$bulan = $request->get('bulan');
		}

		$fy = $request->get('fy');
		if(strlen($request->get('fy')) == 0){
			$date = db::table('weekly_calendars')
			->where('week_date', date('Y-m-d'))
			->first();

			$fy = $date->fiscal_year;
		}

		$hpl = "";
		if($request->get('fy') == "all"){
			$hpl = "";
		}else if($request->get('fy') == "askey"){
			$hpl = "and materials.hpl = 'ASKEY'";
		}else if($request->get('fy') == "tskey"){
			$hpl = "and materials.hpl = 'ASKEY'";
		}

		$monthly = db::select("select bulan.bulan, ng.qty as ng, cek.qty as cek, ROUND((coalesce(ng.qty, 0) / coalesce(cek.qty, 0) * 100),2) as ng_rate from 
			(select distinct date_format(week_date, '%Y-%m') as bulan from weekly_calendars
			where fiscal_year = '".$fy."'
			) as bulan
			left join
			(select date_format(welding_ng_logs.created_at, '%Y-%m') as bulan, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where welding_ng_logs.location = '".$loc."'
			".$hpl."
			group by date_format(welding_ng_logs.created_at, '%Y-%m')
			) as ng
			on bulan.bulan = ng.bulan
			left join
			(select date_format(welding_check_logs.created_at, '%Y-%m') as bulan, sum(welding_check_logs.quantity) as qty from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where welding_check_logs.location = '".$loc."'
			".$hpl."
			group by date_format(welding_check_logs.created_at, '%Y-%m')
			) as cek
			on bulan.bulan = cek.bulan
			order by bulan.bulan asc");


		$weekly = db::select("SELECT weeks.week_name, sum(ng.ng) as ng, sum(cek.cek) as cek, round((sum(ng.ng)/sum(cek.cek)*100),2) as ng_rate from
			(SELECT week_name, week_date from weekly_calendars
			where DATE_FORMAT(week_date,'%m-%Y') = '".$bulan."') weeks
			left join
			(SELECT date(welding_ng_logs.created_at) as tgl, sum(welding_ng_logs.quantity) ng from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			".$hpl."
			GROUP BY tgl) ng
			on weeks.week_date = ng.tgl
			left join
			(SELECT date(welding_check_logs.created_at) as tgl, sum(welding_check_logs.quantity) cek from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_check_logs.created_at,'%m-%Y') = '".$bulan."'
			".$hpl."
			GROUP BY tgl) cek
			on weeks.week_date = cek.tgl
			GROUP BY weeks.week_name");

		$daily_alto = db::select("SELECT dates.week_date, ng.ng, cek.cek , round((COALESCE(ng.ng,0)/cek.cek*100),2) as ng_rate from
			(SELECT week_date from weekly_calendars
			where DATE_FORMAT(week_date,'%m-%Y') = '".$bulan."') dates
			left join
			(SELECT date(welding_ng_logs.created_at) as tgl, sum(welding_ng_logs.quantity) ng from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			and materials.hpl = 'ASKEY'
			GROUP BY tgl) ng
			on dates.week_date = ng.tgl
			left join
			(SELECT date(welding_check_logs.created_at) as tgl, sum(welding_check_logs.quantity) cek from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_check_logs.created_at,'%m-%Y') = '".$bulan."'
			and materials.hpl = 'ASKEY'
			GROUP BY tgl) cek
			on dates.week_date = cek.tgl");

		$daily_tenor = db::select("SELECT dates.week_date, ng.ng, cek.cek , round((COALESCE(ng.ng,0)/cek.cek*100),2) as ng_rate from
			(SELECT week_date from weekly_calendars
			where DATE_FORMAT(week_date,'%m-%Y') = '".$bulan."') dates
			left join
			(SELECT date(welding_ng_logs.created_at) as tgl, sum(welding_ng_logs.quantity) ng from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			and materials.hpl = 'TSKEY'
			GROUP BY tgl) ng
			on dates.week_date = ng.tgl
			left join
			(SELECT date(welding_check_logs.created_at) as tgl, sum(welding_check_logs.quantity) cek from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_check_logs.created_at,'%m-%Y') = '".$bulan."'
			and materials.hpl = 'TSKEY'
			GROUP BY tgl) cek
			on dates.week_date = cek.tgl");


		$response = array(
			'status' => true,
			'monthly' => $monthly,
			'weekly' => $weekly,
			'daily_alto' => $daily_alto,
			'daily_tenor' => $daily_tenor,
			'fy' => $fy,
			'bulan' => $bulan
		);
		return Response::json($response);
		
	}


	public function fetchWeldingKeyResume(Request $request){

		$loc = $request->get('loc');
		
		$bulan = date('m-Y');
		if(strlen($request->get('bulan')) > 0){
			$bulan = $request->get('bulan');
		}

		$askey = db::select("select cek.`key`, cek.hpl, cek.qty as cek, ng.qty as ng, round((coalesce(ng.qty,0)/cek.qty*100),2) as ng_rate from
			(select materials.`key`, materials.hpl, sum(welding_check_logs.quantity) as qty from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where date_format(welding_check_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_check_logs.location = '".$loc."'
			and materials.hpl = 'ASKEY'
			group by materials.`key`, materials.hpl) cek
			left join
			(select materials.`key`, materials.hpl, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where date_format(welding_ng_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'ASKEY'
			group by materials.`key`, materials.hpl) ng
			on cek.`key` = ng.`key` and cek.hpl = ng.hpl
			order by ng desc
			limit 10");


		$askey_detail = db::select("select ng_name.`key`, ng_name.ng_name, COALESCE(ng.jml,0) as jml from  
			(select b.`key`, a.ng_name from
			(select ng_name from ng_lists
			where location = '".$loc."') a
			cross join
			(select distinct `key` from materials
			where `key` != ''
			and origin_group_code = '043') b
			order by `key` asc) ng_name
			left join
			(select materials.`key`, welding_ng_logs.ng_name, sum(welding_ng_logs.quantity) as jml from welding_ng_logs
			left join materials on welding_ng_logs.material_number = materials.material_number
			where DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'ASKEY'
			group by materials.`key`, welding_ng_logs.ng_name) ng
			on ng_name.ng_name = ng.ng_name and ng_name.`key` = ng.`key`
			order by `key` asc");


		$tskey = db::select("select cek.`key`, cek.hpl, cek.qty as cek, ng.qty as ng, round((coalesce(ng.qty,0)/cek.qty*100),2) as ng_rate from
			(select materials.`key`, materials.hpl, sum(welding_check_logs.quantity) as qty from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where date_format(welding_check_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_check_logs.location = '".$loc."'
			and materials.hpl = 'TSKEY'
			group by materials.`key`, materials.hpl) cek
			left join
			(select materials.`key`, materials.hpl, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where date_format(welding_ng_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'TSKEY'
			group by materials.`key`, materials.hpl) ng
			on cek.`key` = ng.`key` and cek.hpl = ng.hpl
			order by ng desc
			limit 10");


		$tskey_detail = db::select("select ng_name.`key`, ng_name.ng_name, COALESCE(ng.jml,0) as jml from  
			(select b.`key`, a.ng_name from
			(select ng_name from ng_lists
			where location = '".$loc."') a
			cross join
			(select distinct `key` from materials
			where `key` != ''
			and origin_group_code = '043') b
			order by `key` asc) ng_name
			left join
			(select materials.`key`, welding_ng_logs.ng_name, sum(welding_ng_logs.quantity) as jml from welding_ng_logs
			left join materials on welding_ng_logs.material_number = materials.material_number
			where DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'TSKEY'
			group by materials.`key`, welding_ng_logs.ng_name) ng
			on ng_name.ng_name = ng.ng_name and ng_name.`key` = ng.`key`
			order by `key` asc");


		$response = array(
			'status' => true,
			'askey' => $askey,
			'tskey' => $tskey,
			'askey_detail' => $askey_detail,
			'tskey_detail' => $tskey_detail,
			'bulan' => $bulan
		);
		return Response::json($response);

	}


	public function fetchWeldingNgResume(Request $request){
		$loc = $request->get('loc');
		
		$bulan = date('m-Y');
		if(strlen($request->get('bulan')) > 0){
			$bulan = $request->get('bulan');
		}
		
		$askey_ng = db::select("select welding_ng_logs.ng_name, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where date_format(welding_ng_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'ASKEY'
			group by welding_ng_logs.ng_name
			order by qty desc");

		$tskey_ng = db::select("select welding_ng_logs.ng_name, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where date_format(welding_ng_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'TSKEY'
			group by welding_ng_logs.ng_name
			order by qty desc");

		$response = array(
			'status' => true,
			'askey_ng' => $askey_ng,
			'tskey_ng' => $tskey_ng,
			'bulan' => $bulan
		);
		return Response::json($response);

	}
	

	public function fetchNgRate(Request $request){
		$now = date('Y-m-d');

		// $ngs = WeldingNgLog::leftJoin('materials', 'materials.material_number', '=', 'welding_ng_logs.material_number')
		// ->orderBy('welding_ng_logs.created_at', 'asc');
		// $checks = WeldingCheckLog::leftJoin('materials', 'materials.material_number', '=', 'welding_check_logs.material_number')
		// ->orderBy('welding_check_logs.created_at', 'asc');
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
			// $ngs = $ngs->whereRaw('date(welding_ng_logs.created_at) = "'.$now.'"');
			// $checks = $checks->whereRaw('date(welding_check_logs.created_at) = "'.$now.'"');
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

		// $ngs = $ngs->get();
		// $checks = $checks->get();


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
			// 'checks' => $checks,
			// 'ngs' => $ngs,
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

		$ng_target = db::table("middle_targets")
		->where('location', '=', 'wld')
		->where('target_name', '=', 'NG Rate')
		->select('target')
		->first();

		$ng_rate = db::select("select eg.`group` as shift, eg.employee_id as operator_id, concat(SPLIT_STRING(e.`name`, ' ', 1),' ',SPLIT_STRING(e.`name`, ' ', 2)) as `name`, rate.`check`, rate.ng, rate.rate from employee_groups eg left join 
			(select c.operator_id, c.jml as `check`, COALESCE(ng.jml,0) as ng, ROUND((COALESCE(ng.jml,0)/c.jml*100),1) as rate 
			from (select w.operator_id, sum(w.quantity) as jml from welding_check_logs w
			left join materials mt on mt.material_number = w.material_number
			where w.operator_id is not null
			".$addlocation."
			and DATE(w.welding_time)='".$now."'
			GROUP BY w.operator_id) c
			left join
			(select w.operator_id, sum(w.quantity) as jml from welding_ng_logs w
			left join materials mt on mt.material_number = w.material_number
			where w.operator_id is not null
			".$addlocation."
			and DATE(w.welding_time)='".$now."'
			GROUP BY w.operator_id) ng
			on c.operator_id = ng.operator_id) rate
			on rate.operator_id = eg.employee_id
			left join employee_syncs e on e.employee_id = eg.employee_id
			where eg.location = 'soldering'
			ORDER BY eg.`group`, e.`name` asc");

		$target = db::select("select eg.`group`, eg.employee_id, e.name, ng.material_number, concat(m.model, ' ', m.`key`) as `key`, ng.ng_name, ng.quantity, ng.created_at from employee_groups eg left join 
			(select * from welding_ng_logs where deleted_at is null ".$addlocation." and remark in 
			(select remark.remark from
			(select operator_id, max(remark) as remark from welding_ng_logs where DATE(welding_time) ='".$now."' ".$addlocation." group by operator_id) 
			remark)
			) ng 
			on eg.employee_id = ng.operator_id
			left join materials m on m.material_number = ng.material_number
			left join employee_syncs e on e.employee_id = eg.employee_id
			where eg.location = 'soldering'
			order by eg.`group`, e.`name` asc");

		$operator = db::select("select g.group, g.employee_id, e.name from employee_groups g
			left join employee_syncs e on e.employee_id = g.employee_id
			where g.location = 'soldering'
			order by g.`group`, e.name asc");

		// $dateTitle = date("d M Y", strtotime($now));

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
			'dateTitle' => $now,
			'title' => $location
		);
		return Response::json($response);
	}

	public function fetchOpRateDetail(Request $request){
		$tgl = $request->get('tgl');
		$nik = (explode(" - ",$request->get('nama')));

		$nama = Employee::where('employee_id','=',$nik[0])->select('name')->first();

		$data_log = db::connection('welding')->select("select * from
			(select hsa.hsa_kito_code, 'HSA' as part_type, m.model, m.`key`, p.perolehan_start_date as `start`, p.perolehan_finish_date as finish, ROUND(TIMESTAMPDIFF(SECOND,p.perolehan_start_date,p.perolehan_finish_date)/60,2) as act, ROUND((s.time * p.perolehan_jumlah)/60,2) as std, perolehan_jumlah from t_perolehan p
			left join m_operator op on op.operator_id = p.operator_id
			left join m_hsa hsa on hsa.hsa_id = p.part_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			left join ympimis.standard_times s on s.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and date(p.tanggaljam) = '".$tgl."'
			and op.operator_nik = '".$nik[0]."'
			union
			select phs.phs_code, 'PHS' as part_type, m.model, m.`key`, p.perolehan_start_date as `start`, p.perolehan_finish_date as finish, ROUND(TIMESTAMPDIFF(SECOND,p.perolehan_start_date,p.perolehan_finish_date)/60,2) as act, ROUND((s.time * p.perolehan_jumlah)/60,2) as std, perolehan_jumlah from t_perolehan p
			left join m_operator op on op.operator_id = p.operator_id
			left join m_phs phs on phs.phs_id = p.part_id
			left join ympimis.materials m on m.material_number = phs.phs_code
			left join ympimis.standard_times s on s.material_number = phs.phs_code
			where p.part_type = '1'
			and date(p.tanggaljam) = '".$tgl."'
			and op.operator_nik = '".$nik[0]."') result 
			order by `start` asc");

		$good = db::select("select l.welding_time, l.material_number, m.model, m.`key`, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as op_kensa, quantity from welding_logs l
			left join materials m on m.material_number = l.material_number
			left join employees e on e.employee_id = l.employee_id
			where l.operator_id = '".$nik[0]."'
			and date(l.welding_time) = '".$tgl."'
			order by welding_time asc");

		$ng = db::select("SELECT a.welding_time, a.material_number, a.model, a.`key`, a.remark, concat(SPLIT_STRING(a.op_kensa, ' ', 1), ' ', SPLIT_STRING(a.op_kensa, ' ', 2)) as op_kensa, SUM(quantity) as quantity from (
			select l.welding_time, l.material_number, m.model, m.`key`, l.remark, e.`name` as op_kensa, l.quantity as quantity from welding_ng_logs l
			left join materials m on m.material_number = l.material_number
			left join employees e on e.employee_id = l.employee_id
			where l.operator_id = '".$nik[0]."'
			and date(l.welding_time) = '".$tgl."') a   GROUP BY remark order by welding_time asc");

		$cek = db::select("SELECT l.welding_time, l.material_number, m.model, m.`key`, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as op_kensa, quantity FROM welding_check_logs l
			left join materials m on l.material_number = m.material_number
			left join employees e on e.employee_id = l.employee_id
			where operator_id = '".$nik[0]."'
			and date(l.welding_time) = '".$tgl."'
			order by welding_time asc");

		$ng_ng = db::select("select l.welding_time, l.material_number, m.model, m.`key`, l.ng_name, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as op_kensa, l.quantity as quantity from welding_ng_logs l
			left join materials m on m.material_number = l.material_number
			left join employees e on e.employee_id = l.employee_id
			where l.operator_id = '".$nik[0]."'
			and date(l.welding_time) = '".$tgl."'");


		$ng_qty = db::Select("select ng_name, sum(quantity) as qty from welding_ng_logs
			where operator_id = '".$nik[0]."'
			and date(welding_time) = '".$tgl."'
			GROUP BY ng_name
			Order by qty desc");

		$response = array(
			'status' => true,
			'nik' => $nik[0],
			'nama' => $nama['name'],
			'data_log' => $data_log,
			'good' => $good,
			'ng' => $ng,
			'cek' => $cek,
			'ng_ng' => $ng_ng,
			'ng_qty' => $ng_qty,
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

		$actual = db::connection('welding')->select("
			SELECT
			DATE( d.tanggaljam ) AS tgl,
			SUM(IF(TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date) < 60, TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date), 0)) AS time,
			COUNT( DISTINCT d.operator_id ) AS op,
			ROUND(( SUM(IF(TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date) < 60, TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date), 0))) / COUNT( DISTINCT d.operator_id ), 2 ) AS act_time,
			ROUND(( SUM(IF(TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date) < 60, TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date), 0))), 2 ) AS all_time,
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
			SUM(IF(TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date) < 60, TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date), 0)))/ COUNT(DISTINCT d.operator_id),
			2 
			) AS loss_time,
			ROUND((
			SELECT
			(SUM( perolehan_jumlah * time.time )/ 60) + 
			(SELECT COALESCE(SUM((perolehan_jumlah * time.time)) / 60,0) 
			FROM
			t_perolehan
			LEFT JOIN m_phs ON m_phs.phs_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_phs.phs_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering' 
			) 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_hsa.hsa_kito_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering'
			)/ COUNT( DISTINCT d.operator_id ),
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
			(SUM( perolehan_jumlah * time.time )/ 60)+
			(SELECT COALESCE(SUM((perolehan_jumlah * time.time)) / 60,0) 
			FROM
			t_perolehan
			LEFT JOIN m_phs ON m_phs.phs_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_phs.phs_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering' 
			) 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_hsa.hsa_kito_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering'
			)/ COUNT( DISTINCT d.operator_id ),
			2 
			) AS loss_time_std,
			ROUND((
			SELECT
			(SUM( perolehan_jumlah * time.time )/ 60)+
			(SELECT COALESCE(SUM((perolehan_jumlah * time.time)) / 60,0) 
			FROM
			t_perolehan
			LEFT JOIN m_phs ON m_phs.phs_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_phs.phs_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering' 
			) 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_hsa.hsa_kito_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering'
			),
			2
			) AS all_time_std  
			FROM
			t_perolehan d
			LEFT JOIN ympimis.weekly_calendars ON weekly_calendars.week_date = DATE_FORMAT( d.tanggaljam, '%Y-%m-%d' )
			JOIN m_device ON m_device.device_id = d.device_id
			JOIN m_mesin ON m_device.mesin_id = m_mesin.mesin_id
			WHERE
			DATE( d.tanggaljam ) BETWEEN '".$from."' AND '".$now."' 
			AND d.flow_id = '1'
			AND d.perolehan_start_date != '2000-01-01 00:00:00'
			AND weekly_calendars.remark <> 'H' 
			AND m_mesin.department_id = '2'
			AND d.operator_id != 0
			GROUP BY tgl");

		// $op = db::connection('welding')->select("select DATE(d.tanggaljam_shift) as tgl, SUM(durasi) as act, count(distinct id_operator) as op from t_data_downtime d where DATE_FORMAT(d.tanggaljam_shift,'%Y-%m-%d') between '".$from."' and '".$now."' and  `status` = '1' GROUP BY tgl");

		// $emp = db::select("select g.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name` from employee_groups g left join employees e on e.employee_id = g.employee_id
		// 	where g.location = 'soldering'");

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

		$eff_target = db::table("middle_targets")
		->where('location', '=', 'wld')
		->where('target_name', '=', 'Operator Efficiency')
		->select('target')
		->first();

		$rate = db::select("select op.employee_id, concat(SPLIT_STRING(op.`name`, ' ', 1),' ',SPLIT_STRING(op.`name`, ' ', 2)) as `name`, op.`group`, eff.eff, rate.post, (eff.eff * COALESCE(rate.post,1) * 100) as oof  from
			(select g.employee_id, e.`name`, g.`group` from ympimis.employee_groups g
			left join ympimis.employee_syncs e
			on g.employee_id = e.employee_id
			where location = 'soldering' and g.deleted_at is null) op
			left join
			(select solder.operator_nik, sum(solder.actual) as actual, sum(solder.std) as std, sum(solder.std)/sum(solder.actual) as eff from
			(select time.operator_nik, sum(time.actual) actual, sum(time.std) std from
			(select op.operator_nik, (TIMESTAMPDIFF(second,p.perolehan_start_date,p.perolehan_finish_date)) as  actual,
			(s.time * p.perolehan_jumlah) as std from soldering_db.t_perolehan p
			left join soldering_db.m_operator op on p.operator_id = op.operator_id
			left join soldering_db.m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.standard_times s on  s.material_number = hsa.hsa_kito_code
			where p.flow_id = '1'
			and p.part_type = '2'
			and date(p.tanggaljam) = '".$date."') time
			group by time.operator_nik
			union all
			select time.operator_nik, sum(time.actual) actual, sum(time.std) std from
			(select op.operator_nik, (TIMESTAMPDIFF(second,p.perolehan_start_date,p.perolehan_finish_date)) as  actual,
			(s.time * p.perolehan_jumlah) as std from soldering_db.t_perolehan p
			left join soldering_db.m_operator op on p.operator_id = op.operator_id
			left join soldering_db.m_phs phs on p.part_id = phs.phs_id
			left join ympimis.standard_times s on  s.material_number = phs.phs_code
			where p.flow_id = '1'
			and p.part_type = '1'
			and date(p.tanggaljam) = '".$date."') time
			group by time.operator_nik) solder
			group by solder.operator_nik) eff
			on op.employee_id = eff.operator_nik
			left join
			(select cek.operator_id, cek.cek, COALESCE(ng.ng,0) as ng, (cek.cek - COALESCE(ng.ng,0)) as good, ((cek.cek - COALESCE(ng.ng,0))/cek.cek) as post from
			(select operator_id, sum(quantity) as cek from ympimis.welding_check_logs
			where date(welding_time) = '".$date."'
			group by operator_id) cek
			left join
			(select operator_id, sum(quantity) as ng from ympimis.welding_ng_logs
			where date(welding_time) = '".$date."'
			group by operator_id) ng
			on cek.operator_id = ng.operator_id) rate
			on op.employee_id = rate.operator_id
			order by `group`, `name` asc");

		$response = array(
			'status' => true,
			'date' => $date,
			'rate' => $rate,
			'eff_target' => $eff_target->target,
		);
		return Response::json($response);

	}

	public function fetchWeldingEffOngoing(Request $request){
		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$target = db::connection('welding_controller')->select("SELECT op.`group`, datas.operator_nik, e.`name`, datas.part_type, datas.material_number, m.model, m.`key`,	datas.sedang, ceil( s.time * v.lot_completion / 60 ) AS std FROM
			(SELECT g.employee_id, e.`name`, g.`group` FROM ympimis.employee_groups g
			LEFT JOIN ympimis.employees e ON e.employee_id = g.employee_id
			WHERE g.location = 'soldering' ) op
			LEFT JOIN
			(SELECT m.mesin_id, m.ws_id, m.mesin_nama, 'PHS' AS part_type, op.operator_nik, m.order_id_sedang_gmc AS material_number, p.proses_sedang_start_date AS sedang FROM m_mesin m
			LEFT JOIN t_proses p ON p.proses_id = m.order_id_sedang
			LEFT JOIN m_operator op ON op.operator_id = m.operator_id
			WHERE	m.flow_id = 1
			AND p.part_type = 1
			UNION
			SELECT m.mesin_id, m.ws_id, m.mesin_nama, 'HSA' AS part_type, op.operator_nik, m.order_id_sedang_gmc AS material_number, p.proses_sedang_start_date AS sedang FROM m_mesin m
			LEFT JOIN t_proses p ON p.proses_id = m.order_id_sedang
			LEFT JOIN m_operator op ON op.operator_id = m.operator_id
			WHERE m.flow_id = 1 
			AND p.part_type = 2) AS datas
			ON op.employee_id = datas.operator_nik
			LEFT JOIN ympimis.materials m ON m.material_number = datas.material_number
			LEFT JOIN ympimis.employee_syncs e ON e.employee_id = datas.operator_nik
			LEFT JOIN ympimis.standard_times s ON s.material_number = datas.material_number
			LEFT JOIN ympimis.material_volumes v ON v.material_number = datas.material_number
			ORDER BY op.`group`, op.`name` ASC");

		$response = array(
			'status' => true,
			'date' => $date,
			'target' => $target,
		);
		return Response::json($response);
	}


	public function fetchWeldingOpEffTarget(Request $request){
		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$eff_target = db::table("middle_targets")
		->where('location', '=', 'wld')
		->where('target_name', '=', 'Operator Efficiency')
		->select('target')
		->first();

		$target = db::connection('welding')->select("select op.`group`, op.employee_id, op.`name`, eff.material_number, CONCAT(m.model,' ',m.`key`) as `key`, eff.finish, eff.act, (std.time*eff.qty) as std, (std.time*eff.qty/eff.act) as eff from
			(select g.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`,
			g.`group` from ympimis.employee_groups g
			left join ympimis.employees e on e.employee_id = g.employee_id
			where g.location = 'soldering') op
			left join
			(select op.operator_nik, dl.part_type, hsa.hsa_kito_code as hsa_material_number, phs.phs_code as phs_material_number, IF(dl.part_type = 1, phs.phs_code, hsa.hsa_kito_code) as material_number, dl.finish, dl.perolehan_jumlah, perolehan_jumlah as qty, dl.act, ((dl.perolehan_jumlah * hsa.hsa_timing)/dl.act) as eff from
			(select a.operator_id, a.part_type, a.part_id, time(a.perolehan_finish_date) as finish, timestampdiff(second, a.perolehan_start_date, a.perolehan_finish_date) as act, a.perolehan_jumlah from
			(select * from t_perolehan
			where date(tanggaljam) = '".$date."'
			and flow_id = '1') a
			left join
			(select * from t_perolehan
			where date(tanggaljam) = '".$date."'
			and flow_id = '1') b
			on (a.operator_id = b.operator_id and a.perolehan_finish_date < b.perolehan_finish_date)
			where b.perolehan_finish_date is null
			order by a.operator_id asc) dl
			left join m_operator op on op.operator_id = dl.operator_id
			left join m_hsa hsa on hsa.hsa_id = dl.part_id
			left join m_phs phs on phs.phs_id = dl.part_id) eff
			on op.employee_id = eff.operator_nik
			left join ympimis.materials m on eff.material_number = m.material_number
			left join ympimis.standard_times std on std.material_number = eff.material_number
			order by op.`group`, op.`name` asc");

		$response = array(
			'status' => true,
			'date' => $date,
			'target' => $target,
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
				$push_data[$i] = db::connection('welding')->select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
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

				$push_data_z[$i] = db::connection('welding')->select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
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
				$push_data[$i] = db::connection('welding')->select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
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

				$push_data_z[$i] = db::connection('welding')->select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
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
				$push_data[$i] = db::connection('welding')->select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
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

				$push_data_z[$i] = db::connection('welding')->select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
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
			$jam = date('Y-m-d H:i:s');
			if ($jam > date('Y-m-d').' 00:00:01' && $jam < date('Y-m-d').' 02:00:00' && $tgl == date('Y-m-d')) {
				$nextday =  date('Y-m-d', strtotime($tgl));
			}else{
				$nextday =  date('Y-m-d', strtotime($tgl . " +1 days"));
			}
		}else{
			$tgl = date("Y-m-d");
			$jam = date('Y-m-d H:i:s');
			if ($jam > date('Y-m-d').' 00:00:01' && $jam < date('Y-m-d').' 02:00:00') {
				$nextday = date('Y-m-d');
			}else{
				$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
			}
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
			and TIME(p.tanggaljam) > '06:00:00'
			and TIME(p.tanggaljam) < '15:00:00'
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
			and TIME(p.tanggaljam) > '16:30:00'
			and p.tanggaljam < '".$nextday." 00:50:00'
			and m.hpl = 'ASKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$alto = db::connection('welding')->select($query1);

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
			and TIME(p.tanggaljam) > '06:00:00'
			and TIME(p.tanggaljam) < '15:00:00'
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
			and TIME(p.tanggaljam) > '16:30:00'
			and p.tanggaljam < '".$nextday." 00:50:00'
			and m.hpl = 'TSKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$tenor = db::connection('welding')->select($query2);
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
			WHERE ".$tanggal." TIME(l.created_at) > '06:00:00' and TIME(l.created_at) < '15:00:00' and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE ".$tanggal." TIME(l.created_at) > '16:30:00' and l.created_at < '".$nextday." 00:50:00' and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
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
			WHERE ".$tanggal." TIME(l.created_at) > '06:00:00' and TIME(l.created_at) < '15:00:00' and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE ".$tanggal." TIME(l.created_at) > '16:30:00' and l.created_at < '".$nextday." 00:50:00' and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$tenor = db::select($query2);
		}

		$query3 = "select distinct `key` from materials where hpl = 'ASKEY' and issue_storage_location = 'SX21' order by `key`";
		$key =  db::select($query3);

		
		if(strpos($addlocation, 'phs')){
			$query4 = "select distinct model from materials where hpl = 'ASKEY' and mrpc = 'S11' and surface = 'PHS' order by model";
			$query5 = "select distinct model from materials where hpl = 'TSKEY' and mrpc = 'S11' and surface = 'PHS' order by model";
		}else{
			$query4 = "select distinct model from materials where hpl = 'ASKEY' and mrpc = 'S21' order by model";
			$query5 = "select distinct model from materials where hpl = 'TSKEY' and mrpc = 'S21' order by model";
		}

		$model_alto =  db::select($query4);
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

	public function scanWeldingJig(Request $request){

		try {
			if ($request->get('status') == 'Repair') {
				$jig = Jig::join('jig_kensas','jig_kensas.jig_id','jigs.jig_id')
				->where('jigs.jig_tag', '=', $request->get('tag'))
				->where('category',$request->get('category'))
				->where('status',$request->get('status'))
				->get();

				if (count($jig) > 0) {
					foreach ($jig as $key) {
						$jig_id = $key->jig_id;
					}

					$part = JigBom::where('jig_boms.jig_parent',$jig_id)->join('jig_part_stocks','jig_boms.jig_child','jig_part_stocks.jig_id')->get();
				}
			}else{
				$jig = Jig::where('jigs.jig_tag', '=', $request->get('tag'))->where('category',$request->get('category'))->first();
				if (count($jig) > 0) {
					$part = JigBom::where('jig_boms.jig_parent',$jig->jig_id)->get();
				}
			}

			if (count($jig) > 0) {
				$response = array(
					'status' => true,
					'jig' => $jig,
					'part' => $part,
					'started_at' => date('Y-m-d H:i:s'),
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Tag Tidak Ditemukan'
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingScheduleJig(Request $request){
		$query = "SELECT
		jigs.jig_id,
		jigs.jig_name,
		jigs.jig_index,
		jigs.category,
		jig_schedules.check_period - 5 AS check_period,
		jig_schedules.last_check,
		DATEDIFF( now( ), last_check ) AS age 
		FROM
		`jig_schedules`
		LEFT JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
		AND jigs.jig_index = jig_schedules.jig_index 
		HAVING
		check_period < age";

		$schedules = db::select($query);

		$response = array(
			'status' => true,
			'schedules' => $schedules,
		);
		return Response::json($response);
	}

	public function fetchJigCheck(Request $request)
	{
		try {
			$jig_id = $request->get('jig_id');

			$jig_check = JigKensaCheck::where('jig_id',$jig_id)->orderBy('check_index','asc')->get();

			$response = array(
				'status' => true,
				'jig_check' => $jig_check,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchDrawingList(Request $request)
	{
		try {
			$jig_id = $request->get('jig_id');

			$drawing = DB::SELECT("SELECT
				a.file_name,
				a.jig_name,
				a.jig_child,
				a.jig_parent
				FROM
				(
				SELECT
				'".$jig_id."' AS jig_parent,
				'".$jig_id."' AS jig_child,
				file_name,
				jig_name 
				FROM
				jigs 
				WHERE
				jig_id = '".$jig_id."' UNION ALL
				SELECT
				jig_parent,
				jig_child,
				file_name,
				jig_name 
				FROM
				`jig_boms`
				JOIN jigs ON jigs.jig_id = jig_child 
				WHERE
				jig_parent = '".$jig_id."' 
			) a");

			$path = '/jig/drawing/';
			$file_path = asset($path);

			$response = array(
				'status' => true,
				'drawing' => $drawing,
				'file_path' => $file_path
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputKensaJig(Request $request)
	{
		try {
			$operator_id = $request->get('operator_id');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$started_at = $request->get('started_at');
			$check_indexes = $request->get('check_indexes');
			$check_index = $request->get('check_index');
			$check_name = $request->get('check_name');
			$upper_limit = $request->get('upper_limit');
			$lower_limit = $request->get('lower_limit');
			$value = $request->get('value');
			$result = $request->get('result');
			$jig_child = $request->get('jig_child');
			$jig_alias = $request->get('jig_alias');
			$started_at = $request->get('started_at');
			$finished_at = date('Y-m-d H:i:s');
			$now = date('Y-m-d');

			$id_user = Auth::id();

			$count_ng = 0;

			for ($j=0; $j < $check_indexes; $j++) { 
				if ($result[$j] == 'NG') {
					$count_ng++;
				}
			}

			if ($count_ng > 0) {
				for ($i=0; $i < $check_indexes; $i++) { 
					$kensa_jig = new JigKensa([
						'operator_id' => $operator_id,
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'check_index' => $check_index[$i],
						'check_name' => $check_name[$i],
						'upper_limit' => $upper_limit[$i],
						'lower_limit' => $lower_limit[$i],
						'value' => $value[$i],
						'result' => $result[$i],
						'jig_child' => $jig_child[$i],
						'jig_alias' => $jig_alias[$i],
						'status' => 'Repair',
						'started_at' => $started_at,
						'finished_at' => $finished_at,
						'created_by' => $id_user,
					]);
					$kensa_jig->save();
				}

				$status = 'Repair';
			}else{
				for ($k=0; $k < $check_indexes; $k++) { 
					$kensa_jig = new JigKensaLog([
						'operator_id' => $operator_id,
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'check_index' => $check_index[$k],
						'check_name' => $check_name[$k],
						'upper_limit' => $upper_limit[$k],
						'lower_limit' => $lower_limit[$k],
						'value' => $value[$k],
						'result' => $result[$k],
						'jig_child' => $jig_child[$k],
						'jig_alias' => $jig_alias[$k],
						'started_at' => $started_at,
						'finished_at' => $finished_at,
						'created_by' => $id_user,
					]);
					$kensa_jig->save();
				}

				$status = 'OK';
			}

			$jigs = Jig::where('jig_id',$jig_id)->first();
			$check_period = $jigs->check_period;

			$jig_schedule = JigSchedule::where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
			if (count($jig_schedule) > 0) {
				if ($status == 'OK') {
					$jig_schedule->schedule_date = date('Y-m-d');
					$jig_schedule->kensa_time = $finished_at;
					$jig_schedule->kensa_status = 'Finish Kensa';
					$jig_schedule->kensa_pic = $operator_id;
					$jig_schedule->repair_status = 'No Repair';
					$jig_schedule->schedule_status = 'Close';
					$jig_schedule->save();

					$schedule = new JigSchedule([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => date('Y-m-d', strtotime($now. ' + '.$check_period.' days')),
						'schedule_status' => 'Open',
						'created_by' => $id_user,
					]);
					$schedule->save();
				}else{
					$jig_schedule->schedule_date = date('Y-m-d');
					$jig_schedule->kensa_time = $finished_at;
					$jig_schedule->kensa_pic = $operator_id;
					$jig_schedule->save();
				}
			}else{
				if ($status == 'OK') {
					$schedule = new JigSchedule([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => date('Y-m-d'),
						'kensa_time' => $finished_at,
						'kensa_pic' => $operator_id,
						'kensa_status' => 'Finish Kensa',
						'repair_status' => 'No Repair',
						'schedule_status' => 'Close',
						'created_by' => $id_user,
					]);
					$schedule->save();

					$schedule = new JigSchedule([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => date('Y-m-d', strtotime($now. ' + '.$check_period.' days')),
						'schedule_status' => 'Open',
						'created_by' => $id_user,
					]);
					$schedule->save();
				}else{
					$schedule = new JigSchedule([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => date('Y-m-d'),
						'kensa_time' => $finished_at,
						'kensa_pic' => $operator_id,
						'kensa_status' => 'Finish Kensa',
						'repair_status' => 'Unrepaired',
						'schedule_status' => 'Open',
						'created_by' => $id_user,
					]);
					$schedule->save();
				}
			}

			$response = array(
				'status' => true,
				'message' => 'Save Kensa Success',
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputRepairJig(Request $request)
	{
		try {
			$operator_id = $request->get('operator_id');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$started_at = $request->get('started_at');
			$check_indexes = $request->get('check_indexes');
			$check_index = $request->get('check_index');
			$check_name = $request->get('check_name');
			$upper_limit = $request->get('upper_limit');
			$lower_limit = $request->get('lower_limit');
			$value = $request->get('value');
			$result = $request->get('result');
			$jig_child = $request->get('jig_child');
			$jig_alias = $request->get('jig_alias');
			$action = $request->get('action');
			$jig_parent = $request->get('jig_parent');
			$part = $request->get('part');
			$count = $request->get('count');
			$status = $request->get('status');
			$finished_at = date('Y-m-d H:i:s');
			$now = date('Y-m-d');
			$id_user = Auth::id();

			$jig_parents = [];
			$jig_childs = [];
			$jig_aliases = [];

			if ($status == 'Repaired') {
				for ($k=0; $k < $check_indexes; $k++) { 
					if ($result[$k] == 'NG') {
						$jig_aliases[] = $jig_alias[$k];
						$jig_childs[] = $jig_child[$k];
						$jig_parents[] = $jig_id;
					}
				}

				$usageserror = 0;

				for ($i = 0; $i < count($jig_aliases); $i++) {
					$jigbom = JigBom::where('jig_parent',$jig_parents[$i])->where('jig_child',$jig_childs[$i])->first();
					$usage = $jigbom->usage;

					$partsstock = JigPartStock::where('jig_id',$jig_aliases[$i])->get();

					foreach ($partsstock as $key) {
						$id_partstock = $key->id;
						$stock = $key->quantity;
						$min_order = $key->min_order;
						$material_jig = $key->material;
						$remarkjig = $key->remark;
					}

					if ($stock < $usage) {
						$usageserror++;
					}else{
						if ($remarkjig == null) {
							$date = date('Y-m-d');
							$prefix_now = 'WJO'.date("y").date("m");
							$code_generator = CodeGenerator::where('note','=','wjo')->first();
							if ($prefix_now != $code_generator->prefix){
								$code_generator->prefix = $prefix_now;
								$code_generator->index = '0';
								$code_generator->save();
							}

							$jigs = Jig::where('jig_id',$jig_childs[$i])->get();

							foreach ($jigs as $key) {
								$item_name = $key->jig_name;
							}

							$sub_section = 'Welding Process_Koshuha Solder';
							$item_name = $item_name;
							$category = 'Jig';
							$drawing_name = $item_name;
							$item_number = $jig_childs[$i];
							$part_number = $jig_aliases[$i];
							$quantity = $min_order;
							$priority = 'Normal';
							$type = 'Pembuatan Baru';
							$material = $material_jig;
							$problem_desc = 'Pembuatan Part Kensa Jig Welding';

							$remark;
							if($priority == 'Normal'){
								$remark = 1;
							}else{
								$remark = 0;
							}

							$request_date = date('Y-m-d', strtotime($date. ' + 14 days'));

							$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
							$order_no = $code_generator->prefix . $number;
							$code_generator->index = $code_generator->index+1;
							$code_generator->save();

							$file_name = $order_no.'.pdf';
							$path = public_path(). '/workshop';
							$file_path = public_path() . "/jig/drawing/" .$jig_parents[$i].'/'.$jig_childs[$i].'.pdf';

							$newplace  = $path.'/'.$order_no.'.pdf';
						    copy($file_path,$newplace);

							$wjo = new WorkshopJobOrder([
								'order_no' => $order_no,
								'sub_section' => $sub_section,
								'item_name' => $item_name,
								'category' => $category,
								'drawing_name' => $drawing_name,
								'item_number' => $item_number,
								'part_number' => $part_number,
								'quantity' => $quantity,
								'target_date' => $request_date,
								'priority' => $priority,
								'type' => $type,
								'material' => $material,
								'problem_description' => $problem_desc,
								'remark' => $remark,
								'attachment' => $file_name,
								'created_by' => 'PI9902015',
							]);

							$wjo_log = new WorkshopJobOrderLog([
								'order_no' => $order_no,
								'remark' => $remark,
								'created_by' => 'PI9902015',
							]);

							$wjo->save();
							$wjo_log->save();

							$partsstocks = JigPartStock::find($id_partstock);
							$qtynew = $stock - $usage;
							$partsstocks->quantity = $qtynew;
							$partsstocks->quantity_order = $min_order;
							$partsstocks->remark = $order_no;
							$partsstocks->save();
						}
					}
				}

				if ($usageserror > 0) {
					$status = false;
					$message = 'Part Tidak Tersedia';
				}else{

					$partsstocks = JigPartStock::find($id_partstock);
					$qtynew = $stock - $usage;
					$partsstocks->quantity = $qtynew;
					$partsstocks->save();

					for ($j=0; $j < $check_indexes; $j++) {

						$repair = new JigRepairLog([
							'operator_id' => $operator_id,
							'jig_id' => $jig_id,
							'jig_index' => $jig_index,
							'check_index' => $check_index[$j],
							'check_name' => $check_name[$j],
							'upper_limit' => $upper_limit[$j],
							'lower_limit' => $lower_limit[$j],
							'value' => $value[$j],
							'result' => $result[$j],
							'jig_child' => $jig_child[$j],
							'jig_alias' => $jig_alias[$j],
							'status' => 'Repaired',
							'action' => $action[$j],
							'started_at' => $started_at,
							'finished_at' => $finished_at,
							'created_by' => $id_user,
						]);
						$repair->save();
					}

					$jigKensa = JigKensa::where('jig_kensas.jig_id', '=', $jig_id)
					->where('status','Repair')
					->get();

					foreach ($jigKensa as $key) {
						$kensa_jig = new JigKensaLog([
							'operator_id' => $key->operator_id,
							'jig_id' => $key->jig_id,
							'jig_index' => $key->jig_index,
							'jig_alias' => $key->jig_alias,
							'check_index' => $key->check_index,
							'check_name' => $key->check_name,
							'upper_limit' => $key->upper_limit,
							'lower_limit' => $key->lower_limit,
							'value' => $key->value,
							'result' => $key->result,
							'status' => 'Repaired',
							'jig_child' => $key->jig_child,
							'started_at' => $key->started_at,
							'finished_at' => $key->finished_at,
							'created_by' => $id_user,
						]);
						$kensa_jig->save();
						$kensas = JigKensa::where('id',$key->id)->forceDelete();
					}

					$jigses = Jig::where('jig_id',$jig_id)->first();
					$check_period = $jigses->check_period;

					$jig_schedule = JigSchedule::where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
					if (count($jig_schedule) > 0) {
							$jig_schedule->repair_time = $finished_at;
							$jig_schedule->repair_pic = $operator_id;
							$jig_schedule->repair_status = 'Finish Repair';
							$jig_schedule->schedule_status = 'Close';
							$jig_schedule->save();

							$schedule = new JigSchedule([
								'jig_id' => $jig_id,
								'jig_index' => $jig_index,
								'schedule_date' => date('Y-m-d', strtotime($now. ' + '.$check_period.' days')),
								'schedule_status' => 'Open',
								'created_by' => $id_user,
							]);
							$schedule->save();
					}

					$status = true;
					$message = 'Repair Jig Selesai';
				}
			}else{
				for ($k=0; $k < $check_indexes; $k++) { 
					// $kensa = new JigKensa([
					// 	'operator_id' => $operator_id,
					// 	'jig_id' => $jig_id,
					// 	'jig_index' => $jig_index,
					// 	'check_index' => $check_index[$k],
					// 	'check_name' => $check_name[$k],
					// 	'upper_limit' => $upper_limit[$k],
					// 	'lower_limit' => $lower_limit[$k],
					// 	'value' => $value[$k],
					// 	'result' => $result[$k],
					// 	'jig_child' => $jig_child[$k],
					// 	'status' => 'Open',
					// 	'action' => $action[$k],
					// 	'started_at' => $started_at,
					// 	'finished_at' => $finished_at,
					// 	'created_by' => $id_user,
					// ]);
					$kensa = JigKensa::where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('check_index',$check_index[$k])->first();
					$kensa->action = $action[$k];
					$kensa->save();
				}

				$jigses = Jig::where('jig_id',$jig_id)->first();
				$check_period = $jigses->check_period;

				$jig_schedule = JigSchedule::where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
				if (count($jig_schedule) > 0) {
					$jig_schedule->repair_time = $finished_at;
					$jig_schedule->repair_pic = $operator_id;
					$jig_schedule->repair_status = 'Waiting Part';
					$jig_schedule->save();
				}

				$status = true;
				$message = 'Jig Belum di Repair. Menunggu Part';

				// $jigKensa = JigKensa::where('jig_kensas.jig_id', '=', $jig_id)
				// 	->where('status','Repair')
				// 	->get();

				// foreach ($jigKensa as $key) {
				// 	$kensa_jig = new JigKensaLog([
				// 		'operator_id' => $key->operator_id,
				// 		'jig_id' => $key->jig_id,
				// 		'jig_index' => $key->jig_index,
				// 		'check_index' => $key->check_index,
				// 		'check_name' => $key->check_name,
				// 		'upper_limit' => $key->upper_limit,
				// 		'lower_limit' => $key->lower_limit,
				// 		'value' => $key->value,
				// 		'result' => $key->result,
				// 		'status' => 'Repaired',
				// 		'jig_child' => $key->jig_child,
				// 		'started_at' => $key->started_at,
				// 		'finished_at' => $key->finished_at,
				// 		'created_by' => $id_user,
				// 	]);
				// 	$kensa_jig->save();

				// 	$kensas = JigKensa::where('id',$key->id)->forceDelete();
				// }
			}

			$response = array(
				'status' => $status,
				'message' => $message,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => $status,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWldJigMonitoring()
	{
		try {

			$monitoring = DB::SELECT("SELECT
				b.bulan,
				SUM( b.before_kensa ) AS before_kensa,
				SUM( b.after_kensa ) AS after_kensa,
				SUM( b.before_repair ) AS before_repair,
				SUM( b.waiting_part ) AS waiting_part 
			FROM
				(
				SELECT DISTINCT
					(
					DATE_FORMAT( week_date, '%Y-%m' )) AS bulan,
					(
					SELECT
						COUNT(
						DISTINCT ( id )) 
					FROM
						jig_schedules 
					WHERE
						DATE_FORMAT( jig_schedules.schedule_date, '%Y-%m' ) = bulan 
						AND schedule_status = 'Open' 
						AND kensa_time IS NULL 
						AND repair_time IS NULL 
					) AS before_kensa,
					(
					SELECT
						COUNT(
						DISTINCT ( id )) 
					FROM
						jig_schedules 
					WHERE
						DATE_FORMAT( jig_schedules.schedule_date, '%Y-%m' ) = bulan 
						AND schedule_status = 'Close' 
						AND kensa_time IS NOT NULL 
					) AS after_kensa,
					(
					SELECT
						COUNT(
						DISTINCT ( id )) 
					FROM
						jig_schedules 
					WHERE
						DATE_FORMAT( jig_schedules.schedule_date, '%Y-%m' ) = bulan 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL 
					) AS before_repair,
					(
					SELECT
						COUNT(
						DISTINCT ( id )) 
					FROM
						jig_schedules 
					WHERE
						DATE_FORMAT( jig_schedules.schedule_date, '%Y-%m' ) = bulan 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL 
					) AS waiting_part 
				FROM
					weekly_calendars 
				WHERE
					week_date BETWEEN DATE(
					DATE_ADD( NOW(), INTERVAL - 6 MONTH )) 
					AND DATE(
					DATE_ADD( NOW(), INTERVAL + 1 MONTH )) UNION ALL
				SELECT
					DATE_FORMAT( jig_schedules.schedule_date, '%Y-%m' ) AS bulan,
					COUNT(
					DISTINCT ( id )) AS before_kensa,
					0 AS after_kensa,
					0 AS before_repair,
					0 AS waiting_part 
				FROM
					jig_schedules 
				WHERE
					schedule_date < DATE(
					DATE_ADD( NOW(), INTERVAL - 6 MONTH )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NULL 
				GROUP BY
					DATE_FORMAT( jig_schedules.schedule_date, '%Y-%m' ) 
				) b 
			GROUP BY
				b.bulan 
			ORDER BY
				b.bulan ASC");

			$resume = DB::SELECT("SELECT
				a.week_date,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					jig_schedules.schedule_date = a.week_date 
					AND schedule_status = 'Open' 
					AND kensa_time IS NULL 
					AND repair_time IS NULL 
				) AS before_kensa,
				( SELECT COUNT( DISTINCT ( id )) FROM jig_schedules WHERE jig_schedules.schedule_date = a.week_date AND schedule_status = 'Close' AND kensa_time IS NOT NULL ) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					jig_schedules.schedule_date = a.week_date 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					jig_schedules.schedule_date = a.week_date 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL 
				) AS waiting_part 
			FROM
				weekly_calendars a 
			WHERE
				a.week_date = DATE(
				NOW())");

			$outstanding = DB::SELECT("SELECT b.*
			FROM
			(SELECT
				jig_schedules.jig_id,
				jigs.jig_name,
				COALESCE ( kensa_time, '' ) AS kensa_time,
				COALESCE ( empkensa.name, '' ) AS kensa_pic,
				COALESCE ( kensa_status, '' ) AS kensa_status,
				COALESCE ( repair_time, '' ) AS repair_time,
				COALESCE ( emprepair.name, '' ) AS repair_pic,
				COALESCE ( repair_status, '' ) AS repair_status,
				schedule_status,
				schedule_date 
			FROM
				`jig_schedules`
				LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic
				LEFT JOIN employee_syncs emprepair ON emprepair.employee_id = jig_schedules.repair_pic
				JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
			WHERE
				schedule_status = 'Open' 
				AND schedule_date BETWEEN DATE(
				DATE_ADD( NOW(), INTERVAL - 6 MONTH )) 
				AND DATE(
				DATE_ADD( NOW(), INTERVAL + 1 MONTH )) UNION
			SELECT
				jig_schedules.jig_id,
				jigs.jig_name,
				COALESCE ( kensa_time, '' ) AS kensa_time,
				COALESCE ( empkensa.name, '' ) AS kensa_pic,
				COALESCE ( kensa_status, '' ) AS kensa_status,
				COALESCE ( repair_time, '' ) AS repair_time,
				COALESCE ( emprepair.name, '' ) AS repair_pic,
				COALESCE ( repair_status, '' ) AS repair_status,
				schedule_status,
				schedule_date 
			FROM
				`jig_schedules`
				LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic
				LEFT JOIN employee_syncs emprepair ON emprepair.employee_id = jig_schedules.repair_pic
				JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
			WHERE
				schedule_status = 'Open' 
				AND schedule_date < DATE(
				DATE_ADD( NOW(), INTERVAL - 6 MONTH ))) b ORDER BY b.schedule_date");

			$response = array(
				'status' => true,
				'monitoring' => $monitoring,
				'outstanding' => $outstanding,
				'resume' => $resume,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWldDetailJigMonitoring(Request $request)
	{
		try {
			$date = $request->get('date');
			$status = $request->get('status');

			$detail = [];

			if ($status == 'Belum Kensa') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE_FORMAT(schedule_date,'%Y-%m') = '".$date."' and schedule_status = 'Open' and kensa_time is null");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("select * from jig_schedules join jig_kensa_logs on jig_kensa_logs.jig_id = jig_schedules.jig_id and DATE(finished_at) = schedule_date join jigs on jigs.jig_id = jig_schedules.jig_id where DATE_FORMAT(schedule_date,'%Y-%m') = '".$date."' and schedule_status = 'Open' and jig_schedules.jig_id = '".$key->jig_id."' and jig_schedules.kensa_time is null");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG BELUM KENSA';
			}elseif ($status == 'Sudah Kensa') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE_FORMAT(schedule_date,'%Y-%m') = '".$date."' and schedule_status = 'Close'");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						*,
						CONCAT(
							SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
							' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
					FROM
						jig_schedules
						JOIN jig_kensa_logs ON jig_kensa_logs.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = schedule_date
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
					WHERE
						DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
						AND schedule_status = 'Close' 
						AND jig_schedules.jig_id = '".$key->jig_id."'");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG SUDAH KENSA';
			}elseif ($status == 'Belum Repair') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE_FORMAT(schedule_date,'%Y-%m') = '".$date."' and schedule_status = 'Open' and kensa_time is not null and repair_time is null");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						*,
						CONCAT(
							SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
							' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
					FROM
						jig_schedules
						JOIN jig_kensas ON jig_kensas.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = schedule_date
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
					WHERE
						DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
						AND schedule_status = 'Open' 
						AND jig_schedules.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG BELUM REPAIR';
			}elseif ($status == 'Menunggu Part') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE_FORMAT(schedule_date,'%Y-%m') = '".$date."' and schedule_status = 'Open' and kensa_time is not null and repair_time is not null");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						* ,
						CONCAT(
							SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
							' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
					FROM
						jig_schedules
						JOIN jig_kensas ON jig_kensas.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = schedule_date
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
					WHERE
						DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
						AND schedule_status = 'Open' 
						AND jig_schedules.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG MENUNGGU PART';
			}

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'detail' => $detail,
				'schedule' => $schedule,
				'judul' => $judul,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingJigData(Request $request)
	{
		try {

			$jigs = Jig::select('*','jigs.id as id_jig')->join('jig_boms','jig_boms.jig_child','jigs.jig_id')->orderby('jigs.id','asc')->get();
			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jigs' => $jigs
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function editWeldingJigData(Request $request)
	{
		try {

			$jigs = Jig::select('*','jigs.id as id_jig')->join('jig_boms','jig_boms.jig_child','jigs.jig_id')->orderby('jigs.id','asc')->where('jigs.id',$request->get('id'))->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jigs' => $jigs
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputWeldingJigData(Request $request)
	{
		try {
			$fileData = $request->get('fileData');
			$jig_parent = $request->get('jig_parent');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$jig_name = $request->get('jig_name');
			$jig_alias = $request->get('jig_alias');
			$category = $request->get('category');
			$jig_tag = $request->get('jig_tag');
			$check_period = $request->get('check_period');
			$type = $request->get('type');
			$usage = $request->get('usage');
			$file = $request->file('fileData');

			$tujuan_upload = 'jig/drawing/'.$jig_parent;

          	$filename = $jig_id.'.'.$request->input('extension');
          	$file->move($tujuan_upload,$filename);

          	$jigs = Jig::firstOrNew(['jig_id' => $jig_id, 'jig_index' => $jig_index]);
            $jigs->jig_id = $jig_id;
            $jigs->jig_index = $jig_index;
            $jigs->jig_name = $jig_name;
            $jigs->jig_alias = $jig_alias;
            $jigs->category = $category;
            $jigs->jig_tag = $jig_tag;
            $jigs->check_period = $check_period;
            $jigs->type = $type;
            $jigs->file_name = $filename;
            $jigs->created_by = Auth::id();

			$jigbom = JigBom::firstOrNew(['jig_parent' => $jig_parent, 'jig_child' => $jig_id]);
            $jigbom->jig_parent = $jig_parent;
            $jigbom->jig_child = $jig_id;
            $jigbom->created_by = Auth::id();
            $jigbom->usage = $usage;

            $jigbom->save();
			$jigs->save();

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function updateWeldingJigData(Request $request)
	{
		try {
			$fileData = $request->get('fileData');
			$jig_parent = $request->get('jig_parent');
			$jig_id = $request->get('jig_id');
			$id_jig = $request->get('id_jig');
			$jig_index = $request->get('jig_index');
			$jig_name = $request->get('jig_name');
			$jig_alias = $request->get('jig_alias');
			$category = $request->get('category');
			$jig_tag = $request->get('jig_tag');
			$check_period = $request->get('check_period');
			$type = $request->get('type');
			$usage = $request->get('usage');
			$file = $request->file('fileData');
			$file_name = $request->get('file_name');

			$jigs = Jig::find($id_jig);
            $jigs->jig_id = $jig_id;
            $jigs->jig_index = $jig_index;
            $jigs->jig_name = $jig_name;
            $jigs->jig_alias = $jig_alias;
            $jigs->category = $category;
            $jigs->jig_tag = $jig_tag;
            $jigs->check_period = $check_period;
            $jigs->type = $type;

			if ($file_name != null) {
				$tujuan_upload = 'jig/drawing/'.$jig_parent;
	          	$filename = $jig_id.'.'.$request->input('extension');
	          	$file->move($tujuan_upload,$filename);

	          	$jigs->file_name = $filename;
			}          	

			$jigbom = JigBom::firstOrNew(['jig_parent' => $jig_parent, 'jig_child' => $jig_id]);
            $jigbom->jig_parent = $jig_parent;
            $jigbom->jig_child = $jig_id;
            $jigbom->usage = $usage;

            $jigbom->save();
			$jigs->save();

			$response = array(
				'status' => true,
				'message' => 'Success Update Data'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingJigData($id,$jig_id,$jig_parent)
	{
		try {

			$jigs = Jig::find($id);

			$jigbom = JigBom::where('jig_boms.jig_child',$jig_id)->forceDelete();

			File::delete('jig/drawing/'.$jig_parent.'/'.$jig_id.'.pdf');

			$jigs->forceDelete();

			return redirect('index/welding/jig_data');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingJigBom()
	{
		try {
			$jigbom = JigBom::select('*','jig_boms.id as id_jig_bom')->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_bom' => $jigbom
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputWeldingJigBom(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$usage = $request->get('usage');

          	$jigs = JigBom::firstOrNew(['jig_parent' => $jig_parent, 'jig_child' => $jig_child]);
            $jigs->jig_parent = $jig_parent;
            $jigs->jig_child = $jig_child;
            $jigs->usage = $usage;
            $jigs->created_by = Auth::id();
			$jigs->save();

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function editWeldingJigBom(Request $request)
	{
		try {
			$jigbom = JigBom::where('jig_boms.id',$request->get('id'))->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_bom' => $jigbom
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function updateWeldingJigBom(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$usage = $request->get('usage');
			$id_jig_bom = $request->get('id_jig_bom');

          	$jigs = JigBom::find($id_jig_bom);
            $jigs->jig_parent = $jig_parent;
            $jigs->jig_child = $jig_child;
            $jigs->usage = $usage;
			$jigs->save();

			$response = array(
				'status' => true,
				'message' => 'Success Update Data'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingJigBom($id)
	{
		try {
			$jigbom = JigBom::find($id);

			$jigbom->forceDelete();

			return redirect('index/welding/jig_bom');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingJigSchedule()
	{
		try {
			$jigschedule = JigSchedule::orderBy('schedule_status','desc')->orderBy('schedule_date','asc')->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_schedule' => $jigschedule
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function editWeldingJigSchedule(Request $request)
	{
		try {
			$jigschedule = JigSchedule::where('id',$request->get('id'))->orderBy('schedule_status','desc')->orderBy('schedule_date','asc')->first();

			$response = array(
				'status' => true,
				'jig_schedule' => $jigschedule
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function updateWeldingJigSchedule(Request $request)
	{
		try {
			$jigschedule = JigSchedule::where('id',$request->get('id'))->first();
			$jigschedule->schedule_date = $request->get('schedule_date');
			$jigschedule->save();

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingKensaPoint()
	{
		try {

			$jig_point = JigKensaCheck::orderBy('jig_id','asc')->orderBy('jig_child','asc')->orderBy('check_index','asc')->get();

			$response = array(
				'status' => true,
				'jig_point' => $jig_point,
				'message' => 'Update Data Success'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputWeldingKensaPoint(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$check_name = $request->get('check_name');
			$lower_limit = $request->get('lower_limit');
			$upper_limit = $request->get('upper_limit');

			$jigalias = Jig::where('jig_id',$jig_child)->first();

			$checkindex = JigKensaCheck::where('jig_id',$jig_parent)->orderBy('check_index','desc')->first();

          	$kensapoint = new JigKensaCheck([
				'jig_id' => $jig_parent,
				'jig_child' => $jig_child,
				'jig_alias' => $jigalias->jig_alias,
				'check_index' => $checkindex->check_index+1,
				'check_name' => $check_name,
				'upper_limit' => $upper_limit,
				'lower_limit' => $lower_limit,
				'created_by' => Auth::id(),
			]);
			$kensapoint->save();

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function editWeldingKensaPoint(Request $request)
	{
		try {
			$jig_point = JigKensaCheck::where('id',$request->get('id'))->first();

			$response = array(
				'status' => true,
				'jig_point' => $jig_point
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function updateWeldingKensaPoint(Request $request)
	{
		try {
			$jigpoint = JigKensaCheck::where('id',$request->get('id_jig_point'))->first();
			$jigpoint->jig_id = $request->get('jig_parent');
			if ($jigpoint->jig_child != $request->get('jig_child')) {
				$jigalias = Jig::where('jig_id',$request->get('jig_child'))->first();
				$jigpoint->jig_alias = $jigalias->jig_alias;
			}
			$jigpoint->jig_child = $request->get('jig_child');
			$jigpoint->check_index = $request->get('check_index');
			$jigpoint->check_name = $request->get('check_name');
			$jigpoint->lower_limit = $request->get('lower_limit');
			$jigpoint->upper_limit = $request->get('upper_limit');
			$jigpoint->save();

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingKensaPoint($id)
	{
		try {
			$jigpoint = JigKensaCheck::find($id);

			$jigpoint->forceDelete();

			return redirect('index/welding/kensa_point');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingJigPart()
	{
		try {
			$jig_part = JigPartStock::get();

			$response = array(
				'status' => true,
				'jig_part' => $jig_part,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputWeldingJigPart(Request $request)
	{
		try {

			$jig_id = $request->get('jig_id');
			$quantity = $request->get('quantity');
			$min_stock = $request->get('min_stock');
			$min_order = $request->get('min_order');
			$quantity_order = $request->get('quantity_order');
			$material = $request->get('material');

			$jigs = JigPartStock::where('jig_id',$jig_id)->first();

			if (count($jigs) > 0) {
				$jigs->jig_id = $jig_id;
				$jigs->quantity = $quantity;
				$jigs->min_stock = $min_stock;
				$jigs->min_order = $min_order;
				$jigs->quantity_order = $quantity_order;
				$jigs->material = $material;
				$jigs->save();
			}else{
				$jigpart = new JigPartStock([
					'jig_id' => $jig_id,
					'quantity' => $quantity,
					'min_stock' => $min_stock,
					'min_order' => $min_order,
					'quantity_order' => $quantity_order,
					'material' => $material,
					'created_by' => Auth::id(),
				]);
				$jigpart->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Input Data Success',
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function editWeldingJigPart(Request $request)
	{
		try {
			$jig_part = JigPartStock::where('id',$request->get('id'))->first();
			$response = array(
				'status' => true,
				'jig_part' => $jig_part
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function updateWeldingJigPart(Request $request)
	{
		try {
			$jigpart = JigPartStock::where('id',$request->get('id_jig_part'))->first();
			$jigpart->jig_id = $request->get('jig_id');
			$jigpart->quantity = $request->get('quantity');
			$jigpart->min_stock = $request->get('min_stock');
			$jigpart->min_order = $request->get('min_order');
			$jigpart->material = $request->get('material');
			$jigpart->save();

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingJigPart($id)
	{
		try {
			$jigpart = JigPartStock::find($id);

			$jigpart->forceDelete();

			return redirect('index/welding/jig_part');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	//END Kensa Jig

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

		$data = db::select("select ws.ws_name, material.material_number, material.model, material.`key`, COALESCE(bff.jml,0) as bff, COALESCE(wld.jml,0) as wld from
			(select l.material_number, m.hpl, m.model, m.`key` from 
			(select distinct l.material_number from middle_request_logs l
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$tanggal."'
			union
			select distinct w.material_number from welding_logs w
			where w.location = 'hsa-visual-sx'
			and DATE_FORMAT(w.created_at,'%Y-%m-%d') = '".$tanggal."') l
			left join materials m on l.material_number = m.material_number) as material
			left join
			(select l.material_number, count(l.material_number) as jml, 'bff' as remark from middle_request_logs l
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$tanggal."'
			group by l.material_number) bff
			on material.material_number = bff.material_number
			left join
			(select w.material_number, count(w.material_number) as jml, 'wld' as remark from welding_logs w
			where w.location = 'hsa-visual-sx'
			and DATE_FORMAT(w.created_at,'%Y-%m-%d') = '".$tanggal."'
			group by w.material_number) wld
			on material.material_number = wld.material_number
			left join
			(select ws.ws_id, hsa.hsa_kito_code as material_number, ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id) ws
			on material.material_number = ws.material_number
			order by ws.ws_id, material.`key`, material.model asc");

		$ws = db::connection('welding')->select("select * from m_ws where ws_name in ('WS 1', 'WS 2', 'WS 3', 'WS 4', 'WS 5', 'WS 13', 'WS 14', 'WS 15', 'WS 16', 'WS 1T', 'WS 2T', 'Burner')");

		// $ws = db::connection('welding')->select("select DISTINCT m_hsa.ws_id, m_ws.ws_name  from m_hsa
		// 	left join m_ws on m_hsa.ws_id = m_ws.ws_id
		// 	order by m_ws.ws_id asc");

		$response = array(
			'status' => true,
			'data' => $data,
			'tanggal' => $tanggal,
			'ws' => $ws
		);
		return Response::json($response);
	}

	public function fetchWeldingTrend(Request $request){
		$operators = $request->get('operator');
		$operator = "";

		for($x = 0; $x < count($operators); $x++) {
			$operator = $operator."'".$operators[$x]."'";
			if($x != count($operators)-1){
				$operator = $operator.",";
			}
		}
		$where_op = " and eg.employee_id in (".$operator.") ";


		$condition_week = "date(week_date)";
		$condition_ng = "date(welding_time)";
		$condition_eff = "date(result.tgl)";

		if ($request->get('condition') == "month") {
			$condition_week = "DATE_FORMAT(week_date,'%m-%Y')";
			$condition_ng = "DATE_FORMAT(welding_time,'%m-%Y')";
			$condition_eff = "DATE_FORMAT(result.tgl, '%Y-%m')";
		}

		$op = db::select("select eg.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, eg.`group` from employee_groups eg
			left join employee_syncs e on e.employee_id = eg.employee_id
			where eg.location = 'soldering' ".$where_op."
			order by eg.`group`, e.`name`");


		$ng = db::select("select series.series, series.employee_id, cek.cek, ng.ng, ROUND((COALESCE(ng.ng,0)/cek.cek)*100,2) as ng_rate from
			(select date.series, eg.employee_id from
			(select ".$condition_week." as series from weekly_calendars
			where week_date >= '".$request->get('datefrom')."'
			and week_date <= '".$request->get('dateto')."'
			group by ".$condition_week.") date
			cross join
			(select employee_id from employee_groups
			where location = 'soldering') eg
			) series
			left join
			(select ".$condition_ng." as series, cek.operator_id, sum(cek.quantity) as cek from welding_check_logs cek
			where date(cek.welding_time) >= '".$request->get('datefrom')."'
			and date(cek.welding_time) <= '".$request->get('dateto')."'
			group by ".$condition_ng.", cek.operator_id) cek
			on series.employee_id = cek.operator_id and series.series = cek.series
			left join
			(select ".$condition_ng." as series, ng.operator_id, sum(ng.quantity) as ng from welding_ng_logs ng
			where date(ng.welding_time) >= '".$request->get('datefrom')."'
			and date(ng.welding_time) <= '".$request->get('dateto')."'
			group by ".$condition_ng.", ng.operator_id) ng
			on series.employee_id = ng.operator_id and series.series = ng.series");
		

		$eff = db::connection('welding')->select("select series.series, series.employee_id, eff.eff from
			(select date.series, eg.employee_id from
			(select ".$condition_week." as series from ympimis.weekly_calendars
			where week_date >= '".$request->get('datefrom')."'
			and week_date <= '".$request->get('dateto')."'
			group by ".$condition_week.") date
			cross join
			(select employee_id from ympimis.employee_groups
			where location = 'soldering') eg) series
			left join
			(select date(result.tgl) as series, result.operator_nik, ROUND(sum(result.std)/sum(result.act)*100, 2) as eff from
			(select wld.tgl, wld.operator_nik, wld.material_number, wld.perolehan_jumlah, wld.act, (std.time * wld.perolehan_jumlah) as std from
			(select date(p.tanggaljam) as tgl, op.operator_nik, hsa.hsa_kito_code, phs.phs_code, if(hsa.hsa_kito_code is null,phs.phs_code,hsa.hsa_kito_code) as material_number, p.perolehan_jumlah, TIMESTAMPDIFF(second,p.perolehan_start_date,perolehan_finish_date) as act from t_perolehan p
			left join m_operator op on op.operator_id = p.operator_id
			left join m_hsa hsa on hsa.hsa_id = p.part_id
			left join m_phs phs on phs.phs_id = p.part_id
			where date(p.tanggaljam) >= '".$request->get('datefrom')."'
			and date(p.tanggaljam) <= '".$request->get('dateto')."'
			and p.flow_id = 1
			and op.operator_nik is not null
			) as wld
			left join ympimis.standard_times std on std.material_number = wld.material_number) as result
			group by ".$condition_eff.", result.operator_nik) eff
			on series.employee_id = eff.operator_nik and series.series = eff.series");

		$response = array(
			'status' => true,
			'op' => $op,
			'ng' => $ng,
			'eff' => $eff,
		);
		return Response::json($response);
	}

	public function fetchGroupAchievementDetail(Request $request){
		
		$bff = db::select("select ws.ws_name, l.material_number, m.model, m.`key`, count(l.material_number) as kanban, sum(l.quantity) as jml from middle_request_logs l
			left join
			(select hsa.hsa_kito_code as material_number, ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id) ws
			on ws.material_number = l.material_number
			left join materials m on m.material_number = l.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$request->get('date')."'
			and ws.ws_name = '".$request->get('ws')."'
			group by ws.ws_name, l.material_number, m.model, m.`key`
			order by m.`key`, m.model asc");

		$wld = db::select("select ws.ws_name, l.material_number, m.model, m.`key`, count(l.material_number) as kanban, sum(l.quantity) as jml from welding_logs l
			left join
			(select hsa.hsa_kito_code as material_number, ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id) ws
			on ws.material_number = l.material_number
			left join materials m on m.material_number = l.material_number
			where l.location = 'hsa-visual-sx'
			and DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$request->get('date')."'
			and ws.ws_name = '".$request->get('ws')."'
			group by ws.ws_name, l.material_number, m.model, m.`key`
			order by m.`key`, m.model asc");


		$response = array(
			'status' => true,
			'bff' => $bff,
			'wld' => $wld
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
			(select DATE_FORMAT(w.created_at,'%Y-%m-%d') as tgl, count(w.quantity) as jml from welding_logs w
			left join materials m on m.material_number = w.material_number
			where w.location = 'hsa-visual-sx'
			and DATE_FORMAT(w.created_at,'%Y-%m-%d') in
			(select week_date from weekly_calendars
			where week_name = (select week_name from weekly_calendars where week_date = '".$tanggal."')
			and DATE_FORMAT(week_date,'%Y') = '".$tahun."')
			group by tgl) wld
			left join
			(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, count(l.quantity) as jml from middle_request_logs l
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

	public function fetchWeldingQueue(Request $request){
		$ws = "";
		if($request->get('grup') != null){
			$wss = $request->get('grup');
			$ws = "";

			for($x = 0; $x < count($wss); $x++) {
				$ws = $ws."'".$wss[$x]."'";
				if($x != count($wss)-1){
					$ws = $ws.",";
				}
			}
			$ws = "where m_ws.ws_name in (".$ws.") ";
		}

		$queue = db::connection('welding_controller')->select("select queue.proses_id, m_ws.ws_name, queue.material_number, m.material_description, m.surface, queue.antrian_date from
			(SELECT t_proses.proses_id, COALESCE(m_hsa.hsa_kito_code,m_phs.phs_code) as material_number, COALESCE(m_hsa.ws_id,m_phs.ws_id) as ws_id, antrian_date FROM t_proses
			left join m_hsa on m_hsa.hsa_id = t_proses.part_id
			left join m_phs on m_phs.phs_id = t_proses.part_id
			where t_proses.proses_status = 0
			and t_proses.proses_id not in (select order_id_akan from m_mesin)
			) as queue
			left join m_ws on m_ws.ws_id = queue.ws_id
			left join ympimis.materials m on m.material_number = queue.material_number
			".$ws."
			order by m_ws.ws_id, queue.antrian_date asc");

		return DataTables::of($queue)
		->addColumn('check', function($queue){
			return '<input type="checkbox" class="queue" id="'.$queue->proses_id.'#'.$queue->material_description.'" onclick="showSelected(this)">';
		})
		->rawColumns([ 'check' => 'check'])
		->make(true);
	}

	public function fetchWeldingStock(){


		$stores = db::select("select material.material_number, material.description, COALESCE(inventory.qty,0) as qty from
			(select m.material_number, m.description from kitto.materials m
			where m.location = 'SX21'
			and m.category = 'KEY') as material
			left join
			(select i.material_number, count(i.material_number) as qty from kitto.inventories i
			where i.lot > 0 
			group by i.material_number) as inventory
			on material.material_number = inventory.material_number
			order by material.description asc");

		$queues = db::connection('welding_controller')->select("SELECT m_hsa.hsa_kito_code as material_number, count(m_hsa.hsa_kito_code) as qty FROM t_proses
			left join m_hsa on m_hsa.hsa_id = t_proses.part_id
			where t_proses.proses_status = 0
			and t_proses.part_type = 2
			and t_proses.proses_id not in (select order_id_akan from m_mesin)
			group by m_hsa.hsa_kito_code");


		$wips = array();


		$server_18 = db::select("select materials.material_number, COALESCE(inventory.qty,0) as qty from materials
			left join
			(select material_number, count(material_number) as qty from welding_inventories
			where location like '%hsa%'
			group by material_number) inventory
			on materials.material_number = inventory.material_number
			where materials.mrpc = 's21'
			and materials.hpl like '%KEY%'");

		$server_13 = db::connection('welding_controller')->select("select material_number, sum(qty) as qty from
			(select material_number, count(material_number) as qty from
			(select order_id_sedang_gmc as material_number from m_mesin
			where order_id_sedang_gmc is not null
			and order_id_sedang_gmc <> ''
			union
			select order_id_akan_gmc as material_number from m_mesin
			where order_id_akan_gmc is not null
			and order_id_akan_gmc <> '') solder
			group by material_number
			union
			select m_hsa.hsa_kito_code as material_number, count(m_hsa.hsa_kito_code) as qty from t_before_cuci
			left join m_hsa on m_hsa.hsa_id = t_before_cuci.part_id
			where t_before_cuci.part_type = 2
			and t_before_cuci.order_status = 0
			group by m_hsa.hsa_kito_code
			union all
			select m_hsa.hsa_kito_code, count(m_hsa.hsa_kito_code) as qty from t_cuci
			left join m_hsa_kartu on m_hsa_kartu.hsa_kartu_code = t_cuci.kartu_code
			left join m_hsa on m_hsa.hsa_id = m_hsa_kartu.hsa_id
			where m_hsa.hsa_kito_code is not null
			group by m_hsa.hsa_kito_code) as wip
			group by material_number");

		foreach ($server_18 as $data1) {
			$wip_qty = $data1->qty;
			foreach ($server_13 as $data2) {
				if($data1->material_number == $data2->material_number){
					$wip_qty =  $wip_qty + $data2->qty;
				}
			}

			array_push($wips, [
				'material_number' => $data1->material_number,
				'qty' => $wip_qty
			]);
		}

		// $stock = db::connection('welding')->select("select material.material_number, material.material_description, COALESCE(antrian.qty,0) as antrian, COALESCE(wip.qty,0) as wip, COALESCE(store.qty,0) as store from
		// 	(select * from ympimis.materials
		// 	where mrpc = 's21'
		// 	and hpl like '%key%') material
		// 	left join
		// 	(select i.material_number, count(i.material_number) as qty from kitto.inventories i
		// 	left join kitto.materials m on i.material_number = m.material_number
		// 	where i.lot > 0 
		// 	and m.location = 'SX21'
		// 	and m.category = 'KEY'
		// 	group by i.material_number) store
		// 	on material.material_number = store.material_number
		// 	left join
		// 	(select p.hsa_kito_code, count(p.hsa_kito_code) as qty from t_pesanan p
		// 	where p.is_deleted = 0
		// 	group by p.hsa_kito_code) antrian
		// 	on material.material_number = antrian.hsa_kito_code
		// 	left join
		// 	(select hsa.hsa_kito_code as material_number, qty.qty from
		// 	(select part.part_id, count(part.part_id) as qty  from
		// 	(select m.order_id_sedang as order_id, o.part_type, o.part_id from m_mesin m
		// 	left join t_order o on m.order_id_sedang = o.order_id
		// 	where m.order_id_sedang <> ''
		// 	or m.order_id_sedang <> null
		// 	and o.part_type = '2'
		// 	union
		// 	select m.order_id_akan as order_id, o.part_type, o.part_id from m_mesin m
		// 	left join t_order o on m.order_id_akan = o.order_id
		// 	where m.order_id_akan <> ''
		// 	or m.order_id_akan <> null
		// 	and o.part_type = '2') part
		// 	group by part.part_id) qty
		// 	left join m_hsa hsa on hsa.hsa_id = qty.part_id) wip
		// 	on material.material_number = wip.material_number");
		

		// dd($wips);


		$stock = array();
		foreach ($stores as $store) {
			$queue_qty = 0;
			foreach ($queues as $queue) {
				if($store->material_number == $queue->material_number){
					$queue_qty = $queue->qty;
				}

			}

			$wip_qty = 0;
			foreach ($wips as $wip) {
				if($store->material_number == $wip['material_number']){
					$wip_qty = $wip['qty'];
				}
			}

			array_push($stock, [
				'material_number' => $store->material_number,
				'material_description' => $store->description,
				'store' => $store->qty,
				'wip' => $wip_qty,
				'antrian' => $queue_qty
			]);
		}

		return DataTables::of($stock)->make(true);
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

			$zed_operator = db::connection('welding')->table('m_phs_kartu')
			->leftJoin('t_kensa', 't_kensa.part_id', '=', 'm_phs_kartu.phs_id')
			->leftJoin('m_operator', 'm_operator.operator_id', '=', 't_kensa.operator_id')
			->where('m_phs_kartu.phs_kartu_code', '=', $tag)
			->where('t_kensa.part_type', '=', '1')
			->where('t_kensa.kanban_no', '=', $zed_material->phs_kartu_no)
			->where('t_kensa.kensa_status', '=', '0')
			->orderBy('t_kensa.order_finish_date', 'desc')
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

			$zed_operator = db::connection('welding')->table('m_hsa_kartu')
			->leftJoin('t_kensa', 't_kensa.part_id', '=', 'm_hsa_kartu.hsa_id')
			->leftJoin('m_operator', 'm_operator.operator_id', '=', 't_kensa.operator_id')
			->where('m_hsa_kartu.hsa_kartu_code', '=', $tag)
			->where('t_kensa.part_type', '=', '2')
			->where('t_kensa.kanban_no', '=', $zed_material->hsa_kartu_no)
			->where('t_kensa.kensa_status', '=', '0')
			->orderBy('t_kensa.order_finish_date', 'desc')
			->first();

			$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
			->where('materials.material_number', '=', $zed_material->hsa_kito_code)
			->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
			->first();
		}

		$delete = db::connection('welding_controller')
		->table('t_cuci')
		->where('kartu_code', $tag)
		->delete();

		if($request->get('location') == 'hsa-dimensi-sx'){
			$response = array(
				'status' => true,
				'message' => 'Material ditemukan',
				'material' => $material,
				'opwelding' => $zed_operator,
				'started_at' => date('Y-m-d H:i:s'),
				'attention_point' => asset("/welding/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
				'check_point' => asset("/welding/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
				'check_point_dimensi' => asset("/welding/check_point_dimensi/".$zed_material->hsa_kito_code.".jpg"),
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => true,
				'message' => 'Material ditemukan',
				'material' => $material,
				'opwelding' => $zed_operator,
				'started_at' => date('Y-m-d H:i:s'),
				'attention_point' => asset("/welding/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
				'check_point' => asset("/welding/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg")
			);
			return Response::json($response);
		}
		
		
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
			// if($request->get('loc') == 'hsa-visual-sx'){
			// 	try{
			// 		$m_hsa_kartu = db::connection('welding')->table('m_hsa_kartu')->where('m_hsa_kartu.hsa_kartu_code', '=', $tag)->first();

			// 		$order_id = db::connection('welding')->table('t_order')->where('part_type', '=', '2')
			// 		->where('part_id', '=', $m_hsa_kartu->hsa_id)
			// 		->where('t_order.kanban_no', '=', $m_hsa_kartu->hsa_kartu_no)
			// 		->first();

			// 		$t_order_detail = db::connection('welding')->table('t_order_detail')
			// 		->where('order_id', '=', $order_id->order_id)
			// 		->where('flow_id', '=', '3')
			// 		->where('order_status', '=', '1')
			// 		->update([
			// 			'order_sedang_start_date' => $request->get('started_at'),
			// 			'order_sedang_finish_date' => date('Y-m-d H:i:s'),
			// 			'order_status' => '6'
			// 		]);

			// 		$t_order = db::connection('welding')->table('t_order')->where('part_type', '=', '2')
			// 		->where('part_id', '=', $m_hsa_kartu->hsa_id)
			// 		->where('t_order.kanban_no', '=', $m_hsa_kartu->hsa_kartu_no)
			// 		->update([
			// 			'order_status' => '5'
			// 		]);
			// 	}
			// 	catch(\Exception $e){

			// 	}
			// }
			try{

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

				$welding_inventory = WeldingInventory::updateOrCreate(
					['tag' => $request->get('tag')],
					['material_number' => $request->get('material_number'),
					'location' => $request->get('loc'),
					'quantity' => $request->get('cek'),
					'barcode_number' => $request->get('barcode_number'),
					'last_check' => $request->get('employee_id'),
					'updated_at' => Carbon::now()]
				);

				if($request->get('loc') != 'hsa-visual-sx'){
					if($request->get('kensa_id') != null){
						$delete = db::connection('welding')
						->table('t_kensa')
						->where('kensa_id', $request->get('kensa_id'))
						->delete();
					}
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

	public function inputWeldingQueue(Request $request){


		$material = $request->get('material');
		$material = explode('-', $material);
		$part_id = $material[0];
		$type = $material[1];
		$qty = $request->get('kanban');
		$date = $request->get('date');
		$time = $request->get('time');


		$part_type = '';
		if($type = "HSA"){
			$part_type = '2';
		}else{
			$part_type = '1';
		}


		try {
			for ($i=1; $i <= $qty; $i++) {
				$unik = base_convert(microtime(false), 8, 36);
				$proses_id = substr(str_replace("-","",$date), 2) . str_replace(":","",$time). $i . $unik;

				$queue = db::connection('welding_controller')
				->table('t_proses')
				->insert([
					'proses_id' => $proses_id,
					'part_type' => $part_type,
					'part_id' => $part_id,
					'antrian_date' => date('Y-m-d H:i:s', strtotime($date.' '.$time.':'.$i)),
					'proses_insert_date' => date('Y-m-d H:i:s', strtotime($date.' '.$time.':'.$i)),
					'proses_sedang_start_date' => date('Y-m-d H:i:s', strtotime('2000-01-01 00:00:00')),
					'proses_sedang_finish_date' => date('Y-m-d H:i:s', strtotime('2000-01-01 00:00:00')),
					'proses_status' => '0',
					'kanban_no' => 0,
					'operator_id' => 0,
					'mesin_id' => 0,
					'pesanan_id' => 0,
					'proses_isupdate' => '0',
					'proses_isdelete' => '0',
					'proses_isupload' => '0',
				]);

			}

			$response = array(
				'status' => true
			);
			return Response::json($response);

		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingQueue(Request $request){

		try{

			if($request->get('idx') != null) {
				$where_idx = "";		
				$idxs = $request->get('idx');
				$idx = "";
				for($x = 0; $x < count($idxs); $x++) {
					$idx = $idx."'".$idxs[$x]."'";
					if($x != count($idxs)-1){
						$idx = $idx.",";
					}

					$queue = db::connection('welding_controller')
					->table('t_proses')
					->where('proses_id', $idxs[$x])
					->delete();
				}
			}

			$response = array(
				'status' => true,
				'idx' => $idx,
			);
			return Response::json($response);
		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

	}

	function dec2hex($number){

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