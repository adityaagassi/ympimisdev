<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\WeldingReworkLog;
use App\CodeGenerator;
use App\WeldingNgLog;
use App\WeldingCheckLog;
use App\WeldingTempLog;
use App\WeldingLog;

class WeldingProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
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

		return view('processes.welding.kensa', array(
			'ng_lists' => $ng_lists,
			'loc' => $id,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Process Welding SX')->with('head', 'Welding Process');
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
					'message' => 'Tag material tidak ditemukan',
				);
				return Response::json($response);
			}

			$zed_operator = db::connection('welding')->table('m_phs_kartu')->leftJoin('t_order', 't_order.part_id', '=', 'm_phs_kartu.phs_id')
			->leftJoin('t_order_detail', 't_order_detail.order_id', '=', 't_order.order_id')
			->leftJoin('m_operator', 'm_operator.operator_id', '=', 't_order_detail.operator_id')
			->where('t_order.part_type', '=', '1')
			->where('t_order_detail.flow_id', '=', '1')
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
					'message' => 'Tag material tidak ditemukan',
				);
				return Response::json($response);
			}

			$zed_operator = db::connection('welding')->table('m_hsa_kartu')->leftJoin('t_order', 't_order.part_id', '=', 'm_hsa_kartu.hsa_id')
			->leftJoin('t_order_detail', 't_order_detail.order_id', '=', 't_order.order_id')
			->leftJoin('m_operator', 'm_operator.operator_id', '=', 't_order_detail.operator_id')
			->where('t_order.part_type', '=', '2')
			->where('t_order_detail.flow_id', '=', '1')
			->select('m_operator.operator_nik', 'm_operator.operator_name', 't_order.order_id', 't_order_detail.order_sedang_finish_date')
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