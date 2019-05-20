<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\LogProcessMiddle;
use App\LogNgMiddle;
use App\TagMaterial;
use App\MiddleInventory;

class MiddleProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function indexProcessMiddleSX(){
		return view('processes.middle.index_sx')->with('page', 'Middle Process SX')->with('head', 'Middle Process');
	}

	public function indexProcessMiddleBarrel($id){
		if($id == 'barrel-as'){
			$title = 'Alto Saxophone Tumbling-Barrel';
			// $theads = ['C', 'D', 'E', 'F', 'G', 'H', 'J'];
			$mprc = 'S51';
			$hpl = 'ASKEY';
		}

		return view('processes.middle.barrel', array(
			'title' => $title,
			// 'theads' => $theads,
			'mrpc' => $mprc,
			'hpl' => $hpl,
		))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
	}

	public function indexProcessMiddleKensa($id){
		$ng_lists = DB::table('ng_lists')->where('location', '=', $id)->get();
		$groups = DB::table('middle_groups')->where('location', '=', $id)->get();

		if($id == 'incoming-lcq'){
			$title = 'Incoming Check Saxophone Key Lcq';
		}
		if($id == 'incoming-lcq2'){
			$title = 'Incoming Check Saxophone Key After Treatment Lcq';
		}
		if($id == 'incoming-lcq-body'){
			$title = 'Incoming Check Saxophone Body Lcq';
		}
		if($id == 'incoming-plt-sx'){
			$title = 'Incoming Check Saxophone Plt';
		}
		if($id == 'kensa-lcq'){
			$title = 'Kensa Saxophone Lcq';
		}
		if($id == 'kensa-plt-sx'){
			$title = 'Kensa Saxophone Plt';
		}

		return view('processes.middle.kensa', array(
			'ng_lists' => $ng_lists,
			'groups' => $groups,
			'loc' => $id,
			'title' => $title,
		))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
	}

	public function fetchMiddleBarrel(Request $request){
		$queues = db::table('barrel_queues')
		->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->where('materials.category', '=', 'WIP')
		->where('materials.hpl', '=', $request->get('hpl'))
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->select('materials.model')
		->orderBy('barrel_queues.created_at', 'asc')
		->get();

		$first = explode(" ", $queues);
		$job = substr($first[1], 0, 1);

		$jobs = db::table('barrel_queues')
		->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->where(db::raw('LEFT(SPLIT_STRING(materials.model, " ", 2), 1)'), '=', $job)
		->select('barrel_queues.tag', 'materials.material_description')
		->orderBy('barrel_queues.created_at', 'asc')
		->limit(4)
		->get();

		$response = array(
			'status' => true,
			'queues' => $queues,
			'jobs' => $jobs,
		);
		return Response::json($response);
	}

	public function fetchResultMiddleKensa(Request $request){

		$prodDate = \DateTime::createFromFormat('d/m/Y', $request->get('prodDate'))->format('Y-m-d');

		$queryResume = "select sum(total_ok) as ok, sum(total_ng) as ng, round(sum(total_ng)/sum(total_ok), 2) as rate from
		(
		select 0 as total_ok, sum(qty) as total_ng from log_ng_middles where prod_date = '" . $prodDate . "' and group_code = '" . $request->get('group') . "' and location = '" . $request->get('location') . "'
		union all
		select sum(qty) as total_ok, 0 as total_ng from log_process_middles where prod_date = '" . $prodDate . "' and group_code = '" . $request->get('group') . "' and location = '" . $request->get('location') . "') as a";

		$queryDetail = "select a.model, a.result, b.ng_name, coalesce(c.ng_qty, 0) as ng_qty from
		(
		select materials.model, sum(log_process_middles.qty) as result from log_process_middles left join materials on materials.material_number = log_process_middles.material_number where log_process_middles.prod_date = '" . $prodDate . "' and log_process_middles.group_code = '" . $request->get('group') . "' and log_process_middles.location = '" . $request->get('location') . "' group by materials.model
		) as a
		cross join
		(
		select ng_name from ng_lists where ng_lists.location = '" . $request->get('location') . "'
		) as b
		left join
		(
		select materials.model, log_ng_middles.ng_name, sum(qty) as ng_qty from log_ng_middles left join materials on materials.material_number = log_ng_middles.material_number where prod_date = '" . $prodDate . "' and group_code = '" . $request->get('group') . "' and location = '" . $request->get('location') . "' group by materials.model, log_ng_middles.ng_name
		) as c on c.model = a.model and c.ng_name = b.ng_name
		order by a.model asc, b.ng_name asc";

		$queryNg = "select a.ng_name, coalesce(ng_qty, 0) as ng_qty from
		(
		(select ng_lists.ng_name from ng_lists where ng_lists.location = '" . $request->get('location') . "') as a
		left join 
		(select log_ng_middles.ng_name, sum(log_ng_middles.qty) as ng_qty from log_ng_middles where log_ng_middles.location = '" . $request->get('location') . "' and log_ng_middles.prod_date = '" . $prodDate . "' and log_ng_middles.group_code = '" . $request->get('group') . "' group by ng_name) as b on a.ng_name = b.ng_name
		)
		order by a.ng_name asc";

		$resume = db::select($queryResume);
		$detail = db::select($queryDetail);
		$ng_lists = DB::table('ng_lists')->where('location', '=', $request->get('location'))->select('ng_lists.ng_name')->orderBy('ng_lists.ng_name', 'asc')->get();
		$ng = db::select($queryNg);

		$response = array(
			'status' => true,
			'resume' => $resume,
			'detail' => $detail,
			'ng_lists' => $ng_lists,
			'ng' => $ng,
			'ng_count' => count($ng_lists),
		);
		return Response::json($response);
	}

	public function ScanMiddleKensa(Request $request){
		$id = Auth::id();
		$tag_material = db::table('tag_materials')->where('tag_materials.tag', '=', $request->get('tag'))
		->leftjoin('materials', 'materials.material_number', '=', 'tag_materials.material_number')
		->select('materials.model', 'materials.mrpc', 'materials.material_number', 'tag_materials.tag')
		->first();

		if($tag_material == null){
			$completion = db::connection('mysql2')->table('completions')
			->where('completions.barcode_number', '=', $request->get('tag'))
			->leftjoin('materials', 'materials.id', '=', 'completions.material_id')
			->where('completions.active', '=', '1')
			->where('materials.location', '=', $request->get('sLoc'))
			->select('completions.lot_completion', 'materials.material_number')
			->first();

			if($completion == null){
				$response = array(
					'status' => false,
					'message' => 'Tag material not registered or inactive.',
				);
				return Response::json($response);
			}

			$new_tag = new TagMaterial([
				'tag' => $request->get('tag'),
				'material_number' => $completion->material_number,
				'qty' => $completion->lot_completion ,
				'op_prod' => '-',
				'location' => $request->get('location'),
				'created_by'=> $id,
			]);
			$new_tag->save();

			$tag_material2 = db::table('tag_materials')->where('tag_materials.tag', '=', $request->get('tag'))
			->leftjoin('materials', 'materials.material_number', '=', 'tag_materials.material_number')
			->select('materials.model', 'materials.mrpc', 'materials.material_number', 'tag_materials.tag')
			->first();

			if($tag_material2 == null){
				$response = array(
					'status' => false,
					'message' => 'Material not registered in MIRAI.',
				);
				return Response::json($response);
			}

			$model = $tag_material2->model;
			$tag = $tag_material2->tag;
			$mrpc = $tag_material2->mrpc;
		}
		else{
			$model = $tag_material->model;
			$tag = $tag_material->tag;
			$mrpc = $tag_material->mrpc;
		}
		
		if($mrpc != $request->get('workCenter')){
			$response = array(
				'status' => false,
				'message' => 'Wrong location',
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => true,
				'message' => 'Material found',
				'model' => $model,
				'tag' => $tag,
			);
			return Response::json($response);
		}
	}

	public function inputNgMiddleKensa(Request $request){

		$tag_material = TagMaterial::where('tag_materials.tag', '=', $request->get('tag'))
		->first();

		$ngName = $request->get('ng_name');
		$ngQty = $request->get('ng_qty');
		$count_text = $request->get('count_text');
		$prodDate = \DateTime::createFromFormat('d/m/Y', $request->get('prodDate'))->format('Y-m-d');
		$id = Auth::id();

		for ($i=0; $i < count($ngName); $i++) {
			try{
				$log_ng_middle = new LogNgMiddle([
					'group_code' => $request->get('group'),
					'op_kensa' => $request->get('opKensa'),
					'prod_date' => $prodDate,
					'tag' => $request->get('tag'),
					'material_number' => $tag_material->material_number,
					'location' => $request->get('location'),
					'ng_name' => $ngName[$i],
					'qty' => $ngQty[$i],
					'op_prod' => $tag_material->op_prod,
					'created_by' => $id,
				]);
				$log_ng_middle->save();
				$success_count[] = $count_text[$i];
			}
			catch (QueryException $e){
				$fail_count[] = $count_text[$i];
			}
		}

		if(isset($fail_count)){
			$response = array(
				'status' => false,
				'fail_count' => $fail_count,
				'message' => 'Material NG has some errors',
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => true,
				'success_count' => $success_count,
				'message' => 'Material NG has been inputted',
			);
			return Response::json($response);
		}
	}

	public function inputResultMiddleKensa(Request $request){
		$tag_material = TagMaterial::where('tag_materials.tag', '=', $request->get('tag'))
		->first();

		$prodDate = \DateTime::createFromFormat('d/m/Y', $request->get('prodDate'))->format('Y-m-d');

		try{

			$id = Auth::id();
			$tag_material->location = $request->get('location');
			$log_process_middle = new LogProcessMiddle([
				'group_code' => $request->get('group'),
				'op_kensa' => $request->get('opKensa'),
				'prod_date' => $prodDate,
				'tag' => $request->get('tag'),
				'material_number' => $tag_material->material_number,
				'location' => $request->get('location'),
				'qty' => $tag_material->qty,
				'op_prod' => $tag_material->op_prod,
				'created_by' => $id,
			]);

			$inventory = MiddleInventory::firstOrNew(['location' => $request->get('location'), 'material_number' => $tag_material->material_number]);
			$inventory->quantity = ($inventory->quantity+$tag_material->qty);

			$tag_material->save();
			$log_process_middle->save();
			$inventory->save();

			$response = array(
				'status' => true,
				'message' => 'Material '. $tag_material->material_number .' inputted as production result.',
			);
			return Response::json($response);

		}
		catch (QueryException $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}
}