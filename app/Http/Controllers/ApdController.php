<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use Excel;
use DataTables;
use App\EmployeeSync;
use App\ProtectiveEquipment;
use App\ProtectiveEquipmentLog;


class ApdController extends Controller{

	public function __construct(){
		$this->middleware('auth');

		$this->apd = [
			'Masker N95',
		];

	}

	public function indexAPD(){
		$title = "Alat Pelindung Diri";
		$title_jp = '??';

		return view('apd.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'apds' => $this->apd,
		))->with('page', 'APD')->with('head','APD');
	}

	public function fetchAPD(Request $request){

		$datefrom = date('Y-m-01');
		$dateto = date('Y-m-d');

		if(strlen($request->get('datefrom'))>0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		if(strlen($request->get('dateto'))>0){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}


		$dept = db::select("select e.department, sum(p.quantity) as quantity from protective_equipment_logs p
			left join employee_syncs e on p.operator_id = e.employee_id
			where date_format(p.created_at, '%Y-%m-%d') >= '".$datefrom."'
			and date_format(p.created_at, '%Y-%m-%d') <= '".$dateto."'
			group by e.department
			order by quantity desc");

		$stock = db::select("select * from protective_equipments");

		$response = array(
			'status' => true,
			'dept' => $dept,
			'stock' => $stock,
			'datefrom' => $datefrom,
			'dateto' => $dateto
		);
		return Response::json($response);
	}

	public function fetchAPDDetail(Request $request){
		
		$detail = db::select("select p.created_at, p.operator_id, e.`name`, e.department, p.location, l.`name` as leader, p.quantity from protective_equipment_logs p
			left join employee_syncs e on p.operator_id = e.employee_id
			left join employee_syncs l on p.leader = l.employee_id
			where date_format(p.created_at, '%Y-%m-%d') >= '".$request->get('datefrom')."'
			and date_format(p.created_at, '%Y-%m-%d') <= '".$request->get('dateto')."'
			and e.department like '%".$request->get('department')."%'
			order by p.created_at");

		$response = array(
			'status' => true,
			'detail' => $detail,
			'datefrom' => $request->get('datefrom'),
			'dateto' => $request->get('dateto')
		);
		return Response::json($response);
	}

	public function inputAPD(Request $request){

		$leader = Auth::user()->username;
		$leader = EmployeeSync::where('employee_id', $leader)->first();

		$ppe = ProtectiveEquipment::where('apd_name', $request->get('apd'))
		->where('location', $leader->section)
		->where('quantity', '>', 0)
		->first();
		if(!$ppe){
			$response = array(
				'status' => false,
				'message' => 'Stok Masker N95 Tidak Ada',
			);
			return Response::json($response);
		}
		$ppe->quantity = $ppe->quantity - $request->get('quantity');

		if($ppe->quantity < 0){
			$response = array(
				'status' => false,
				'message' => 'Stok Masker N95 Tidak Cukup',
			);
			return Response::json($response);
		}


		$log = new ProtectiveEquipmentLog([
			'operator_id' => $request->get('employee_id'),
			'apd_name' => $request->get('apd'),
			'location' => $leader->section,
			'quantity' => $request->get('quantity'),
			'leader' => $leader->employee_id
		]);
		
		try{
			DB::transaction(function() use ($ppe, $log){
				$ppe->save();
				$log->save();
			});	

			$response = array(
				'status' => true,
				'message' => 'Data APD Berhasil Disimpan',
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
