<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
// use BARCODE_UPCA\Escpos\PrintConnectors\WindowsPrintConnector;
// use BARCODE_UPCA\Escpos\Printer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Exception;
use Carbon\Carbon;
use App\StorageLocation;
use App\CodeGenerator;
use App\ScrapLocation;
use App\ScrapList;
use App\User;
use App\ScrapLog;
use App\ScrapAdditional;
use App\SapCompletion;
use App\TransactionCompletion;
use App\SapTransactions;
use DataTables;
use DateTime;
use Response;
use PDF;
use Excel;

class ScrapController extends Controller
{
	private $storage_location;
	public function __construct(){
		$this->storage_location = [
			'CL91',
			'CLB9',
			'FL91',
			'SX91',
			'VN91',
			'CL51',
			'FL51',
			'SX51',
			'VN51',
			'CL21',
			'FL21',
			'SX21',
			'VN21',
			'VNA0'
		];

		$this->reicive = [
			'MSCR',
			'WSCR'
		];

		$this->category_reason = [
			'Material Jelek',
			'Material Salah'
		];

		$this->category = [
			'PANTHOM',
			'NON PANTHOM'
		];
	}
	public function displayScrapWarehouse(){

		return view('scrap.display', array(
			'title' => 'Scrap Material',
			'title_jp' => 'スクラップ材料'

		))->with('page', 'Scrap');
	}

	public function MonitoringWip(){

		return view('scrap.monitoring', array(
			'title' => 'Scrap Material',
			'title_jp' => 'スクラップ材料',
			'storage_locations' => $this->storage_location

		))->with('page', 'Scrap');
	}

	public function MonitoringScrapDisplay(){
		$storage_locations = $this->storage_location;

		return view('scrap.display_monitoring', array(
			'title' => 'Scrap Material',
			'title_jp' => 'スクラップ材料',
			'storage_locations' => $storage_locations

		))->with('page', 'Scrap');
	}

	public function indexScrap(){
		$reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');


		return view('scrap.index', array(
			'reason' => $reason,
			'title' => 'Scrap Material',
			'title_jp' => 'スクラップ材料',
			'storage_locations' => $this->storage_location,
			'category_reason' => $this->category_reason,
			'reicive' => $this->reicive,
			'category' => $this->category
		))->with('page', 'Scrap');
	}

	public function ListWip(){
		$reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');


		return view('scrap.list_scrap_wip', array(
			'reason' => $reason,
			'title' => 'Scrap Material',
			'title_jp' => 'スクラップ材料',
			'storage_locations' => $this->storage_location,
			'category_reason' => $this->category_reason,
			'reicive' => $this->reicive,
			'category' => $this->category
		))->with('page', 'Scrap');
	}

	public function indexScrapData(){
		$storage_locations = ScrapLocation::select('location', 'storage_location')->distinct()
		->orderBy('location', 'asc')
		->get();

		return view('scrap.list', array(
			'title' => 'Data Scrap Material',
			'title_jp' => 'スクラップ材料',
			'storage_locations' => $storage_locations
		))->with('page', 'Scrap');
	}

	public function indexScrapView(){

		return view('scrap.view', array(
			'title' => 'Scrap Material',
			'title_jp' => 'スクラップ材料',
			'storage_locations' => $this->storage_location

		))->with('page', 'Scrap');
	}

	public function indexWarehouse(){
		return view('scrap.warehouse', array(
			'title' => 'Scrap Material',
			'title_jp' => 'スクラップ材料',

		))->with('page', 'Scrap');
	}

	public function indexLogs(){
		$materials = db::table('scrap_materials')
		->whereNull('deleted_at')
		->select('material_number', 'material_description as description', 'issue_location')
		->orderBy('issue_location', 'ASC')
		->orderBy('material_number', 'ASC')
		->get();

		return view('scrap.logs', array(
			'title' => 'Scrap Logs',
			'storage_locations' => $this->storage_location,
			'reicive' => $this->reicive,
			'category' => $this->category,
			'category_reason' => $this->category_reason,
			'materials' => $materials
		))->with('page', 'Scrap Logs');
	}

	public function fetchLogs(Request $request){

		$date = '';
		if(strlen($request->get('datefrom')) > 0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$date = "AND date(created_at) >= '".$datefrom."' ";
			if(strlen($request->get('dateto')) > 0){
				$dateto = date('Y-m-d', strtotime($request->get('dateto')));
				$date = $date . "AND date(created_at) <= '".$dateto."' ";
			}
		}

		$date_pending = '';
		if(strlen($request->get('datefrom')) > 0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$date_pending = "AND DATE(sl.created_at) >= '".$datefrom."' ";
			if(strlen($request->get('dateto')) > 0){
				$dateto = date('Y-m-d', strtotime($request->get('dateto')));
				$date_pending = $date_pending . "AND DATE(sl.created_at) <= '".$dateto."' ";
			}
		}

		$issue = '';
		if($request->get('issue') != null){
			$issues =  $request->get('issue');
			for ($i=0; $i < count($issues); $i++) {
				$issue = $issue."'".$issues[$i]."'";
				if($i != (count($issues)-1)){
					$issue = $issue.',';
				}
			}
			$issue = " AND issue_location IN (".$issue.") ";
		}

		$receive = '';
		if($request->get('receive') != null){
			$receives =  $request->get('receive');
			for ($i=0; $i < count($receives); $i++) {
				$receive = $receive."'".$receives[$i]."'";
				if($i != (count($receives)-1)){
					$receive = $receive.',';
				}
			}
			$receive = " AND receive_location IN (".$receive.") ";
		}

		$material = '';
		if($request->get('material') != null){
			$materials =  $request->get('material');
			for ($i=0; $i < count($materials); $i++) {
				$material = $material."'".$materials[$i]."'";
				if($i != (count($materials)-1)){
					$material = $material.',';
				}
			}
			$material = " AND material_number IN (".$material.") ";
		}

		$remark = '';
		if($request->get('remark') != null){
			$remark = " AND remark = '".$request->get('remark')."' ";
		}

		$condition = $date . $issue . $receive . $material . $remark;
		$pending = $date_pending . $issue . $receive . $material . $remark;

		if($request->get('remark') == 'pending'){
			$log = db::select("SELECT
				sl.id,
				CONCAT(sl.slip,'-SC') as slip,
				sl.material_number,
				sl.issue_location,
				sl.receive_location,
				sl.material_description,
				sl.quantity,
				'pending' AS remark,
				sl.created_at AS printed_at,
				u.`name` AS printed_by,
				'-' AS received_at,
				'-' AS received_by,
				'-' AS deleted_at,
				'-' AS deleted_by,
				'-' AS canceled_at,
				'-' AS canceled_by 
				FROM
				scrap_lists AS sl
				LEFT JOIN users AS u ON u.id = sl.created_by 
				WHERE
				sl.deleted_at IS NULL ".$pending."
				ORDER BY
				sl.created_at");
		}
		else{
			$log = db::select("SELECT
				non.id,
				CONCAT(non.slip,'-SC') as slip,
				non.material_number,
				non.issue_location,
				non.receive_location,
				non.material_description,
				non.quantity,
				IF(cancel.remark is null, non.remark, cancel.remark) AS remark,
				non.slip_created AS printed_at,
				scrap_user.`name` AS printed_by,
				IF(non.remark = 'received', non.created_at, '-') AS received_at,
				IF(non.remark = 'received', non_user.`name`, '-') AS received_by,
				IF(non.remark = 'deleted', non.created_at, '-') AS deleted_at,
				IF(non.remark = 'deleted', non_user.`name`, '-') AS deleted_by,
				COALESCE(cancel.created_at, '-') AS canceled_at,
				COALESCE(cancel_user.`name`, '-') AS canceled_by
				FROM
				(SELECT id, scrap_id, slip, material_number, material_description, issue_location, receive_location, quantity, remark, slip_created, scraped_by, created_at, created_by FROM `scrap_logs`
				where remark <> 'canceled' ".$condition." ) AS non
				LEFT JOIN(SELECT id, scrap_id, remark, created_at, created_by FROM `scrap_logs`
				where remark = 'canceled' ".$condition." ) AS cancel
				ON non.scrap_id = cancel.scrap_id
				LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS scrap_user ON scrap_user.id = non.scraped_by
				LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS non_user ON non_user.id = non.created_by
				LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS cancel_user ON cancel_user.id = cancel.created_by
				ORDER BY non.slip_created");
		}

		return DataTables::of($log)
		->addColumn('cancel', function($data){
			if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PROD"){
				if($data->remark == 'pending'){
					return '<button style="width: 50%; height: 100%;" onclick="deleteScrap(\''.$data->id.'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
				}
				else if($data->remark == 'received'){
					return '<button style="width: 50%; height: 100%;" onclick="cancelScrap(\''.$data->id.'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';						
				}
				else{
					return '-';					
				}
			}
			else{
				return '-';
			}
		})
		->rawColumns([ 'cancel' => 'cancel'])
		->make(true);
	}

	public function cancelScrap(Request $request){

		try {

			$scrap = ScrapLog::where('id', '=', $request->get('id'))->first();
			$scrap_logs = new ScrapLog([
				'scrap_id' => $scrap->scrap_id,
				'slip' => $scrap->slip,
				'material_number' => $scrap->material_number,
				'material_description' => $scrap->material_description,
				'spt' => $scrap->spt,
				'valcl' => $scrap->valcl,
				'category' => $scrap->category,
				'issue_location' => $scrap->issue_location,
				'receive_location' => $scrap->receive_location,
				'quantity' => $scrap->quantity,
				'reason' => $scrap->reason,
				'slip_created' => $scrap->slip_created,
				'created_by' => Auth::id(),
				'remark' => 'canceled'
			]);
			$scrap_logs->save();

			$response = array(
				'status' => true,
				'message' => 'Scrap berhasil dicancel',
			);
			return Response::json($response);
			
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}	
	}

	public function deleteScrap(Request $request){
		$auth_id = Auth::id();
		$scrap = ScrapList::where('id', '=', $request->get('id'))->first();
		try{
			$scrap_log = new ScrapLog([
				'scrap_id' => $scrap->id,
				'slip' => $scrap->slip,
				'material_number' => $scrap->material_number,
				'material_description' => $scrap->material_description,
				'spt' => $scrap->spt,
				'valcl' => $scrap->valcl,
				'category' => $scrap->category,
				'issue_location' => $scrap->issue_location,
				'receive_location' => $scrap->receive_location,
				'quantity' => $scrap->quantity,
				'reason' => $scrap->reason,
				'scraped_by' => $scrap->created_by,
				'slip_created' => $scrap->created_at,
				'deleted_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s"),
				'created_by' => Auth::id(),
				'remark' => 'deleted'
			]);
			$scrap_log->save();
			$scrap->forceDelete();
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
			'message' => 'Slip Scrap berhasil di delete',
		);
		return Response::json($response);
	}

	public function fetchScrapDetail(Request $request){

		$today     = date("Y-m-d");
		$date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                  $last = "LAST_DAY(NOW())";
             }else{
                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
                  $last = "'".$date_to."'";
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "LAST_DAY(NOW())";
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
             }
        }

        if (($date_to && $date_from) == null) {
        	$scrap = DB::SELECT("
        	SELECT
				id,
				CONCAT( slip, '-SC' ) AS slip,
				material_description,
				category,
				issue_location,
				quantity,
				reason,
				created_at 
			FROM
				`scrap_logs` 
			WHERE
				remark = 'received'
				AND DATE( scrap_logs.created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )
			ORDER BY
				created_at DESC");
        }
        else{
        	$scrap = DB::SELECT("
        	SELECT
				id,
				CONCAT( slip, '-SC' ) AS slip,
				material_description,
				category,
				issue_location,
				quantity,
				reason,
				created_at 
			FROM
				`scrap_logs` 
			WHERE
				remark = 'received'
				AND DATE( scrap_logs.created_at ) >= ".$first." 
				AND DATE( scrap_logs.created_at ) <= ".$last."
			ORDER BY
				created_at DESC");	
        }
        



		// $scrap = db::table('scrap_logs')
		// ->select(
		// 	'scrap_logs.id',
		// 	DB::raw('CONCAT(slip,"-SC") as slip'),
		// 	'scrap_logs.material_description',
		// 	'scrap_logs.category',
		// 	'scrap_logs.issue_location',
		// 	'scrap_logs.quantity',
		// 	'scrap_logs.reason',	
		// 	'scrap_logs.updated_at')
		// ->distinct('scrap_logs.slip')
		// ->where('scrap_logs.remark', '=', 'received')
		// ->where(db::raw('date(created_at)'), $today)
		// ->orderBy('updated_at', 'desc')
		// ->get();

		 return DataTables::of($scrap)
		 ->make(true);
	}

	public function fetchScrapWarehouse(Request $request)
    {   
    	  $dateto = $request->get('dateto');

          if ($dateto != "") {
		        $resumes = db::select("
	            SELECT
				*
				FROM
				scrap_lists 
				WHERE
				scrap_lists.deleted_at IS NULL
				and scrap_lists.remark = '2'
				and DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
				ORDER BY created_at DESC
	            ");
          } else {
              	$resumes = db::select("
	            SELECT
				*
				FROM
				scrap_lists 
				WHERE
				scrap_lists.deleted_at IS NULL
				and scrap_lists.remark = '2'
				ORDER BY created_at DESC
	            ");
          }
        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response);
    }

	public function scanScrapWarehouse(Request $request){
		$id = $request->get('number');
		$auth_id = Auth::id();
		$scrap = ScrapList::where('slip', '=', $id)->first();

		// if($scrap == null){
		// 	$response = array(
		// 		'status' => false,
		// 		'message' => $e->getMessage(),
		// 	);
		// 	return Response::json($response);
		// }

		$date = date('Y-m-d');

		$cek_data = DB::SELECT("select * from scrap_logs 
		where slip = '".$request->get('number')."'");

		if (count($cek_data) > 0) {
			$response = array(
		       'status' => false,
		       'message' => 'Slip Scrap Sudah Diterima Warehouse'
		 	);
		 	return Response::json($response);
		}
		else {
			if($scrap->remark == 'pending'){
				try{
					$scrap_log = new ScrapLog([
						'scrap_id' => $scrap->id,
						'slip' => $scrap->slip,
						'material_number' => $scrap->material_number,
						'material_description' => $scrap->material_description,
						'spt' => $scrap->spt,
						'valcl' => $scrap->valcl,
						'category' => $scrap->category,
						'issue_location' => $scrap->issue_location,
						'receive_location' => $scrap->receive_location,
						'quantity' => $scrap->quantity,
						'reason' => $scrap->reason,
						'scraped_by' => $scrap->created_by,
						'slip_created' => $scrap->created_at,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						'created_by' => Auth::id(),
						'remark' => 'received'
					]);
					$scrap_log->save();

					if ($scrap->category == 'ASSY') {
						//SAP TRANSACTIONS
					$st = new SapTransactions([
						'entry_date' => $date,
						'posting_date' => $date,
						'movement_type' => '101',
						'material_number' => $scrap->material_number,
						'quantity' => $scrap->quantity,
						// 'storage_location' => $scrap->storage_location,
						'receive_location' => $scrap->receive_location,
						'reference' => 'G'.$scrap->slip.'/'.$scrap->reason,
						'remark' => $scrap->material_description,
						'created_by' => '1'
					]);
					$st->save();
					}
					// else if ($scrap->category == 'SINGLE' && ($scrap->valcl == '9030'||$scrap->valcl == '9010')) {
					// 	//TRANSACTION COPLATIONS (CS)
					// $tc = new TransactionCompletion([
					// 	 'serial_number' => 'G'.$scrap->slip.'/'.$scrap->reason,
					// 	 'material_number' => $scrap->material_number,
					// 	 'issue_plant' => '8190',
					// 	 'issue_location' => $scrap->issue_location,
					// 	 'quantity' => $scrap->quantity,
					// 	 'movement_type' => '101',
					// 	 'created_by' => Auth::id(),
					// 	 'created_at' => date("Y-m-d H:i:s"),
					// 	 'updated_at' => date("Y-m-d H:i:s")
					// ]);
					// $tc->save();
					// }
					else if ($scrap->category == 'SINGLE' && ($scrap->valcl == '9040'||$scrap->valcl == '9041')) {
						//SAP TRANSACTIONS
					$st = new SapTransactions([
						'entry_date' => $date,
						'posting_date' => $date,
						'movement_type' => '9S1',
						'material_number' => $scrap->material_number,
						'quantity' => $scrap->quantity,
						'storage_location' => $scrap->issue_location,
						'receive_location' => $scrap->receive_location,
						'reference' => $scrap->slip.'/'.$scrap->reason,
						'remark' => $scrap->material_description,
						'created_by' => '1'
					]);
					$st->save();
					}				
					$scrap->forceDelete();
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
					'message' => 'Slip scrap berhasil dikonfirmasi',
				);
				return Response::json($response);
			}
			else{
				try{
						$scrap_log = new ScrapLog([
							'scrap_id' => $scrap->id,
							'slip' => $scrap->slip,
							'material_number' => $scrap->material_number,
							'material_description' => $scrap->material_description,
							'spt' => $scrap->spt,
							'valcl' => $scrap->valcl,
							'category' => $scrap->category,
							'issue_location' => $scrap->issue_location,
							'receive_location' => $scrap->receive_location,
							'quantity' => $scrap->quantity,
							'reason' => $scrap->reason,
							'scraped_by' => $scrap->created_by,
							'created_at' => date("Y-m-d H:i:s"),
							'updated_at' => date("Y-m-d H:i:s"),
							'created_by' => Auth::id(),
							'slip_created' => $scrap->created_at,
							'remark' => 'rejected'
					]);
					$scrap_log->save();
					$scrap->forceDelete();
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
					'message' => 'Slip scrap berhasil ditolak',
				);
				return Response::json($response);
			}
		}
	}

	function fetchScrap(Request $request){
		$id = substr($request->get('id'), 2);
		$return = ScrapList::where('scrap_lists.id', '=', $id)
		->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
		->select('scrap_lists.id', 'scrap_lists.material_number', 'scrap_lists.material_description', 'scrap_lists.issue_location', 'scrap_lists.receive_location', 'scrap_lists.quantity', 'users.name', 'scrap_lists.created_at', 'scrap_lists.created_by')
		->first();

		if($scrap == null){
			$response = array(
				'status' => false,
				'message' => "QRcode scrap tidak ditemukan.",
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'scrap' => $scrap,
		);
		return Response::json($response);
	}

	public function fetchScrapList(Request $request){

		$pesan = "";

		$lists = db::table('scrap_materials')
		->whereNull('deleted_at')
		->select('material_number', 'material_description as description', 'issue_location', 'spt', 'valcl')
		->where('issue_location', $request->get('loc'))
		->orderBy('material_number', 'ASC');
		
		if ($request->get('cat') != null) {
			if ($request->get('cat') == 'ASSY') {
	            $lists->where('spt', '=', '50');
			}
			else if($request->get('cat') == 'SINGLE'){
				$lists->whereNull('spt');		
			}

			$pesan = 'Kategory berhasil dipilih';
		}
		else{
			$pesan = 'Lokasi Berhasil dipilih';
		}

		$list_all = $lists->get();

		if(count($list_all) == 0){
			$response = array(
				'status' => false,
				'message' => 'Lokasi terpilih tidak memiliki list material'
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'lists' => $list_all,
			'message' => $pesan
		);
		return Response::json($response);
	}

	public function fetchScrapResume(Request $request){
		$today = date('Y-m-d');

		$resumes = ScrapList::where('issue_location', '=', $request->get('loc'))
		->where('remark', '=', 'pending')
		->where(DB::raw("DATE_FORMAT(scrap_lists.created_at, '%Y-%m-%d')"),$today)
		->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
		->select('scrap_lists.id', 'scrap_lists.slip', 'scrap_lists.material_description', 'scrap_lists.receive_location', 'scrap_lists.category', 'scrap_lists.quantity', 'scrap_lists.remark', 'users.name', DB::RAW('DATE_FORMAT(scrap_lists.created_at,"%d-%m-%Y") as tanggal'), 'scrap_lists.created_by')
		->orderBy('scrap_lists.created_at', 'desc')
		->get();

		$response = array(
			'status' => true,
			'resumes' => $resumes
		);
		return Response::json($response);
	}

	public function ResumeListWip(Request $request){
		try {
			$loc = $request->get('loc');
	        $date_from = $request->get('date_from');
	        $date_to = $request->get('date_to');
	        if ($date_from == "") {
	             if ($date_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "LAST_DAY(NOW())";
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "'".$date_to."'";
	             }
	        }else{
	             if ($date_to == "") {
	                  $first = "'".$date_from."'";
	                  $last = "LAST_DAY(NOW())";
	             }else{
	                  $first = "'".$date_from."'";
	                  $last = "'".$date_to."'";
	             }
	        }

	        if (($date_to && $date_from) == null) {
	        $resumes = DB::SELECT("SELECT
				slip,
				material_description,
				DATE( scrap_lists.created_at ) AS tanggal,
				`name`
			FROM
				`scrap_lists`
			LEFT JOIN users
			ON scrap_lists.created_by = users.id
			WHERE
				issue_location = '".$loc."'
				AND DATE( scrap_lists.created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )");

	        $resumes1 = DB::SELECT("SELECT
				slip,
				material_description,
				DATE( scrap_logs.created_at ) AS tanggal,
				`name`,
				remark
			FROM
				`scrap_logs`
			LEFT JOIN users
			ON scrap_logs.created_by = users.id
			WHERE
				issue_location = '".$loc."'
				AND remark = 'received'
				AND DATE( scrap_logs.created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )");
	        }
	        else{
	        $resumes = DB::SELECT("SELECT
				slip,
				material_description,
				DATE( scrap_lists.created_at ) AS tanggal,
				`name`
			FROM
				`scrap_lists`
			LEFT JOIN users
			ON scrap_lists.created_by = users.id
			WHERE
				issue_location = '".$loc."'
				AND DATE( scrap_lists.created_at ) >= ".$first."
				AND DATE( scrap_lists.created_at ) <= ".$last."");

	        $resumes1 = DB::SELECT("SELECT
				slip,
				material_description,
				DATE( scrap_logs.created_at ) AS tanggal,
				`name`,
				remark
			FROM
				`scrap_logs`
			LEFT JOIN users
			ON scrap_logs.created_by = users.id
			WHERE
				issue_location = '".$loc."'
				AND remark = 'received'
				AND DATE( scrap_logs.created_at ) >= ".$first."
				AND DATE( scrap_logs.created_at ) <= ".$last."");	
	        }

	        

	        $response = array(
	            'status' => true,
	            'resumes' => $resumes,
	            'resumes1' => $resumes1
	        );
	        return Response::json($response);
	      } 
	    catch (\Exception $e) {
	        $response = array(
	            'status' => false,
	            'message' => $e->getMessage()
	        );
	        return Response::json($response);
	      }



		// $resumes = ScrapList::where('issue_location', '=', $request->get('loc'))
		// ->where('remark', '=', 'pending')
		// ->where(DB::raw("DATE_FORMAT(scrap_lists.created_at, '%Y-%m-%d')"),$today)
		// ->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
		// ->select('scrap_lists.id', 'scrap_lists.slip', 'scrap_lists.material_description', 'scrap_lists.issue_location', 'scrap_lists.receive_location', 'scrap_lists.category', 'scrap_lists.quantity', 'scrap_lists.remark', 'users.name', DB::RAW('DATE_FORMAT(scrap_lists.created_at,"%d-%m-%Y") as tanggal'), 'scrap_lists.created_by')
		// ->orderBy('scrap_lists.created_at', 'desc')
		// ->get();

		// $response = array(
		// 	'status' => true,
		// 	'resumes' => $resumes
		// );
		// return Response::json($response);
	}

	public function ResumeListWh(Request $request){
		$today = date('Y-m-d');

		$resumes = ScrapLog::where('issue_location', '=', $request->get('loc'))
		->where('remark', '=', 'received')
		->where(DB::raw("DATE_FORMAT(scrap_logs.created_at, '%Y-%m-%d')"),$today)
		->leftJoin('users', 'users.id', '=', 'scrap_logs.created_by')
		->select('scrap_logs.id', 'scrap_logs.slip', 'scrap_logs.material_description', 'scrap_logs.issue_location', 'scrap_logs.receive_location', 'scrap_logs.category', 'scrap_logs.quantity', 'scrap_logs.remark', 'users.name', DB::RAW('DATE_FORMAT(scrap_logs.created_at,"%d-%m-%Y") as tanggal'), 'scrap_logs.created_by')
		->orderBy('scrap_logs.created_at', 'desc')
		->get();

		$response = array(
			'status' => true,
			'resumes' => $resumes
		);
		return Response::json($response);
	}

	public function fetchScrapListAssy(Request $request){

		if ($request->get('cat') == 'ASSY') {
			$lists = db::table('scrap_materials')
			->whereNull('deleted_at')
			->select('material_number', 'material_description as description', 'issue_location')
			->where('issue_location', $request->get('loc'))
			->where('spt', '=', '50')
			->orderBy('material_number', 'ASC')
			->get();
		}
		else if($request->get('cat') == 'SINGLE'){
			$lists = db::table('scrap_materials')
			->whereNull('deleted_at')
			->select('material_number', 'material_description as description', 'issue_location')
			->where('issue_location', $request->get('loc'))
			->where('spt', '!=', '50')
			->orderBy('material_number', 'ASC')
			->get();	
		}

		

		if(count($lists) == 0){
			$response = array(
				'status' => false,
				'message' => 'Lokasi terpilih tidak memiliki list material'
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'lists' => $lists,
			'message' => 'Lokasi berhasil dipilih'
		);
		return Response::json($response);
	}

	public function printScanWh($category, $material, $description, $quantity, $issue, $slip, $receive_location, $name){
		$connector = new WindowsPrintConnector('MIS');
		$printer = new Printer($connector);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text("SCRAP SLIP"."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("No Slip : ".$slip."-SC\n");
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($category."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($description."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($issue." --> ".$receive_location."\n");
		$printer->text($quantity." PC(s)"."\n");
		$printer->feed(1);
		$printer->setEmphasis(true);
		$printer->feed(1);
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		// $printer->text("Order Number : "."G".$slip.'/'.$reason."\n");
		$printer->qrCode($slip, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
		$printer->feed(1);
		$printer->initialize();
		// $printer->setJustification(Printer::JUSTIFY_CENTER);
		// $printer->text("------------------------------------");
		// $printer->feed(1);
		// $printer->text("|Delivered by: |Confirm'd by Supervisor: |");
		// $printer->feed(1);
		// $printer->text("|              |                		 |");
		// $printer->feed(1);
		// $printer->text("|              |                		 |");
		// $printer->feed(1);
		// $printer->text("------------------------------------");
		// $printer->feed(2);
		// $printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->textRaw("(".date("d-M-Y H:i:s").")\n");
		$printer->text($name."\n");
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}

	public function printTermalPapper($category, $material, $description, $quantity, $issue, $slip, $receive_location, $reason, $name){
		$connector = new WindowsPrintConnector('MIS');
		$printer = new Printer($connector);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text("SCRAP SLIP"."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("No Slip : ".$slip."-SC\n");
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($category."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($description."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($issue." --> ".$receive_location."\n");
		$printer->text($quantity." PC(s)"."\n");
		$printer->feed(1);
		$printer->setEmphasis(true);
		$printer->feed(1);
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->text("Order Number : "."G".$slip.'/'.$reason."\n");
		$printer->qrCode($slip, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
		$printer->feed(1);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("------------------------------------");
		$printer->feed(1);
		$printer->text("|Delivered by: |Confirm'd by Supervisor: |");
		$printer->feed(1);
		$printer->text("|              |                		 |");
		$printer->feed(1);
		$printer->text("|              |                		 |");
		$printer->feed(1);
		$printer->text("------------------------------------");
		$printer->feed(2);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->textRaw("(".date("d-M-Y H:i:s").")\n");
		$printer->text($name."\n");
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}

	public function printAssyPanthom($category, $material, $description, $quantity, $issue, $receive_location, $slip, $reason, $name){
		$connector = new WindowsPrintConnector('MIS');
		$printer = new Printer($connector);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text("SCRAP SLIP"."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("No Slip : ".$slip."-SC\n");
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($category."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($description."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($issue." --> ".$receive_location."\n");
		$printer->text($quantity." PC(s)"."\n");
		$printer->feed(1);
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->text("Order Number : "."G".$slip.'/'.$reason."\n");
		$printer->qrCode('G'.$slip.'/'.$reason, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("GMC : ".$material."\n");
		$printer->setBarcodeHeight(48);
		$isi=$material;
		$type=Printer::BARCODE_CODE39 ;
		$printer->barcode($isi,$type);
		$printer->feed(1);
		$printer->text($quantity." PC(s)"."\n");
		$printer->qrCode($quantity, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
		// $isi = $quantity;
		// $printer->barcode("00000".$isi,$type);
		$printer->feed(1);
		$printer->text("Location : ".$issue."\n");
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setBarcodeHeight(48);
		$isi=$issue;
		// $type=Printer::BARCODE_CODE39;
		$printer->barcode($isi,$type);
		$printer->feed(1);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("------------------------------------");
		$printer->feed(1);
		$printer->text("|Delivered by: |Confirm'd by Supervisor: |");
		$printer->feed(1);
		$printer->text("|              |                		 |");
		$printer->feed(1);
		$printer->text("|              |                		 |");
		$printer->feed(1);
		$printer->text("------------------------------------");
		$printer->feed(2);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->textRaw("(".date("d-M-Y H:i:s").")\n");
		$printer->text($name."\n");
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}

	public function printSinglePanthomAwal($category, $material, $description, $quantity, $issue, $receive_location, $slip, $reason, $name){
		$connector = new WindowsPrintConnector('MIS');
		$printer = new Printer($connector);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text("SCRAP SLIP"."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("No Slip : ".$slip."-SC\n");
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($category."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($description."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($issue." --> ".$receive_location."\n");
		$printer->text($quantity." PC(s)"."\n");
		$printer->feed(1);
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->text("Order Number : ".$slip.'/'.$reason."\n");
		$printer->qrCode($slip.'/'.$reason, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->feed(1);
		$printer->text("Location : ".$issue."\n");
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setBarcodeHeight(50);
		$isi=$issue;
		$type=Printer::BARCODE_CODE39;
		$printer->barcode($isi,$type);
		$printer->feed(1);
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->text("GMC + Qty : ".$material.'+'.$quantity."\n");
		$printer->qrCode($material.'+'.$quantity, Printer::QR_ECLEVEL_L, 8, Printer::QR_MODEL_2);
		$printer->feed(1);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("------------------------------------");
		$printer->feed(1);
		$printer->text("|Delivered by: |Confirm'd by Supervisor: |");
		$printer->feed(1);
		$printer->text("|              |                		 |");
		$printer->feed(1);
		$printer->text("|              |                		 |");
		$printer->feed(1);
		$printer->text("------------------------------------");
		$printer->feed(2);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->textRaw("(".date("d-M-Y H:i:s").")\n");
		$printer->text($name."\n");
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}


	public function printScrap(Request $request){
		$id = Auth::id();

		$prefix_now = date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','scrap')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$slip = $code_generator->prefix . $numbers;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();

		// $date = date('Y-m-d');

		try{
		// if ($request->get('category') == 'ASSY') {
			$scrap = new ScrapList([
				'slip' => $slip,
				'material_number' => $request->get('material'),
				'material_description' => $request->get('description'),
				'issue_location' => $request->get('issue'),
				'category' => $request->get('category'),
				'receive_location' => $request->get('receive_location'),
				'reason' => $request->get('reason'),
				'summary' => $request->get('summary'),
				'quantity' => $request->get('quantity'),
				'spt' => $request->get('spt'),
				'valcl' => $request->get('valcl'),
				'remark' => 'pending',
				'category_reason' => $request->get('category_reason'),
				'created_by' => $id		
			]);
			$scrap->save();
		// }

			if ($scrap->category == 'SINGLE' && ($scrap->valcl == '9030'||$scrap->valcl == '9010')) {
						//TRANSACTION COPLATIONS (CS)
					$tc = new TransactionCompletion([
						 'serial_number' => 'G'.$scrap->slip.'/'.$scrap->reason,
						 'material_number' => $scrap->material_number,
						 'issue_plant' => '8190',
						 'issue_location' => $scrap->issue_location,
						 'quantity' => $scrap->quantity,
						 'movement_type' => '101',
						 'created_by' => Auth::id(),
						 'created_at' => date("Y-m-d H:i:s"),
						 'updated_at' => date("Y-m-d H:i:s")
					]);
					$tc->save();
				}
		// else{
			// $scrap = new ScrapList([
			// 	'slip' => $slip,
			// 	'material_number' => $request->get('material'),
			// 	'material_description' => $request->get('description'),
			// 	'issue_location' => $request->get('issue'),
			// 	'category' => $request->get('category'),
			// 	'receive_location' => $request->get('receive_location'),
			// 	'reason' => $request->get('reason'),
			// 	'summary' => $request->get('summary'),
			// 	'quantity' => $request->get('quantity'),
			// 	'valcl' => $request->get('valcl'),
			// 	'remark' => 'pending',
			// 	'category_reason' => $request->get('category_reason'),
			// 	'created_by' => $id		
			// ]);
			// $scrap->save();

			// $st = new SapTransactions([
			// 	'entry_date' => $date,
			// 	'posting_date' => $date,
			// 	'movement_type' => '9S1',
			// 	'material_number' => $scrap->material_number,
			// 	'quantity' => $scrap->quantity,
			// 	'storage_location' => $scrap->issue_location,
			// 	'receive_location' => $scrap->receive_location,
			// 	'reference' => $scrap->slip.'/'.$scrap->reason,
			// 	'remark' => $scrap->material_description,
			// 	'created_by' => '1'
			// ]);
			// $st->save();
		// }
			// $scrap = new ScrapList([
			// 	'slip' => $slip,
			// 	'material_number' => $request->get('material'),
			// 	'material_description' => $request->get('description'),
			// 	'issue_location' => $request->get('issue'),
			// 	'category' => $request->get('category'),
			// 	'receive_location' => $request->get('receive_location'),
			// 	'reason' => $request->get('reason'),
			// 	'summary' => $request->get('summary'),
			// 	'quantity' => $request->get('quantity'),
			// 	'spt' => $request->get('spt'),
			// 	'valcl' => $request->get('valcl'),
			// 	'remark' => 'pending',
			// 	'category_reason' => $request->get('category_reason'),
			// 	'created_by' => $id		
			// ]);
			// $scrap->save();

			$user = ScrapList::leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
			->first();

			if ($scrap->spt == '50') {
				self :: printAssyPanthom($request->get('category'), $request->get('material'),$request->get('description'), $request->get('quantity'), $request->get('issue'), $request->get('receive_location'), $slip, $request->get('reason'), $user->name);
				// self :: printScanWh($request->get('category'), $request->get('material'),$request->get('description'), $request->get('quantity'), $request->get('issue'), $slip, $request->get('receive_location'), $user->name);
			}
			else if($request->get('category') == 'SINGLE' && ($scrap->valcl == '9040' || $scrap->valcl == '9041')){
				self :: printSinglePanthomAwal($request->get('category'), $request->get('material'),$request->get('description'), $request->get('quantity'), $request->get('issue'), $request->get('receive_location'), $slip, $request->get('reason'), $user->name);
			}
			else{
				self :: printTermalPapper($request->get('category'), $request->get('material'),$request->get('description'), $request->get('quantity'), $request->get('issue'), $slip, $request->get('receive_location'), $request->get('reason'), $user->name);

				$response = array(
				'status' => true,
				'message' => 'CS dilakukan MIRAI di SAP'
				);
				return Response::json($response);

			}
			
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
			'message' => 'Cetak slip scrap berhasil'
		);
		return Response::json($response);
	}

	public function reprintScrap(Request $request){
		try{
			$scrap = ScrapList::where('scrap_lists.id', '=', $request->get('id'))
			->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
			->first();
			self::reprintTermalPapper($scrap->category, $scrap->material_number, $scrap->material_description, $scrap->quantity, $scrap->issue_location, $scrap->slip, $scrap->receive_location, $scrap->name);
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
			'message' => 'Cetak ulang slip scrap berhasil'
		);
		return Response::json($response);
	}

	public function importCompletion(Request $request){
		if($request->hasFile('completion')) {
			try{				
				$file = $request->file('completion');
				$file_name = 'import_cs_'.Auth::id().'('. date("y-m-d") .')'.'.'.$file->getClientOriginalExtension();
				$file->move(public_path('import/completion/'), $file_name);


				$excel = public_path('import/completion/') . $file_name;
				$rows = Excel::load($excel, function($reader) {
					$reader->noHeading();
					$reader->skipRows(1);
				})->get();
				$rows = $rows->toArray();


				// DB::beginTransaction();
				$month = $request->get('date_completion');
				$cc = $request->get('cc');
				$cost_center_name = explode(",",$cc);


				$existing = SapCompletion::leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'sap_completions.storage_location')
				->where(db::raw('DATE_FORMAT(sap_completions.posting_date, "%Y-%m")'), $month)
				->whereIn('storage_locations.cost_center_name', $cost_center_name)
				->delete();


				for ($i=0; $i < count($rows); $i++) {
					$entry_date = $rows[$i][0]->format('Y-m-d');
					$posting_date = $rows[$i][3]->format('Y-m-d');
					$movement_type = $rows[$i][4];
					$material_number = $rows[$i][5];
					$quantity = $rows[$i][8];
					$storage_location = $rows[$i][12];
					$reference = $rows[$i][15];

					$log = new SapCompletion([
						'entry_date' => $entry_date,
						'posting_date' => $posting_date,
						'movement_type' => $movement_type,
						'material_number' => $material_number,
						'quantity' => $quantity,
						'storage_location' => $storage_location,
						'reference' => $reference,
						'created_by' => Auth::id()
					]);
					$log->save();

				}

				// DB::rollback();


				// DB::commit();

				$response = array(
					'status' => true,
					'message' => 'Upload file success'
				);
				return Response::json($response);

			}catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}else{
			$response = array(
				'status' => false,
				'message' => 'Upload failed, File not found',
			);
			return Response::json($response);
		}
	}

	public function updateScrap(Request $request){
		$auth_id = Auth::id();
		$assy = ScrapList::where('id', '=', $request->get('id'))->first();
		try{
			$scrap_log = new ScrapLog([
				'scrap_id' => $assy->id,
				'material_number' => $assy->material_number,
				'material_description' => $assy->material_description,
				'issue_location' => $assy->issue_location,
				'receive_location' => $assy->receive_location,
				'quantity' => $assy->quantity,
				'category' => $assy->category,
				'category_reason' => $assy->category_reason,
				'remark' => '1',
				'reason' => $assy->reason,
				'summary' => $assy->summary,
				'printed_by' => $assy->created_by,
				'printed_at' => $assy->created_at
			]);

			$scrap_log->save();
			
			$assy = ScrapList::find($request->get('id'));
			$assy->remark = '1';
			$assy->save();

			$scrap = ScrapList::where('scrap_lists.id', '=', $request->get('id'))
			->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
			->first();

			// self::printTermalPapper($scrap->category, $scrap->material_number, $scrap->material_description, $scrap->quantity, $scrap->issue_location, $scrap->slip, $scrap->receive_location, $scrap->name);


			$response = array(
				'status' => true,
				'message' => 'Slip scrap berhasil update',
			);

			return Response::json($response);

		}
		catch (QueryException $e){
			return back()->with('error', 'Error')->with('page', 'Category Error');
		}
	}

	public function indexScrapResume(Request $request){

		$lists = db::table('scrap_materials')
		->whereNull('deleted_at')
		->select('material_number', 'material_description as description', 'issue_location')
		->where('issue_location', $request->get('loc'))
		->orderBy('material_number', 'DESC')
		->get();

		$today = date('Y-m-d');

		// $resumes = ScrapList::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"),$today)
		// ->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
		// ->where('scrap_lists.issue_location', $request->get('loc'))
		// ->select('scrap_lists.id',
		// 	'scrap_lists.material_number',
		// 	'scrap_lists.slip', 
		// 	'scrap_lists.material_description',
		// 	'scrap_lists.category',
		// 	'scrap_lists.issue_location', 
		// 	'scrap_lists.quantity', 
		// 	'scrap_lists.reason', 
		// 	'scrap_lists.category_reason',
		// 	'scrap_lists.remark',
		// 	'users.name', 
		// 	DB::RAW('DATE_FORMAT(scrap_lists.created_at,"%d-%m-%Y") as date'))
		// ->orderBy('scrap_lists.created_at', 'DESC')
		// ->get();

		$resumes = ScrapList::where('issue_location', '=', $request->get('loc'))
		// ->where('remark', '!=', '2')
		->leftJoin('users', 'users.id', '=', 'scrap_lists.created_by')
		->select('scrap_lists.id', 'scrap_lists.material_number', 'scrap_lists.slip', 'scrap_lists.material_description', 'scrap_lists.issue_location', 'scrap_lists.category', 'scrap_lists.reason', 'scrap_lists.quantity', 'scrap_lists.remark', 'users.name', DB::RAW('DATE_FORMAT(scrap_lists.created_at,"%d-%m-%Y") as date'), 'scrap_lists.created_by')
		->orderBy('scrap_lists.created_at', 'desc')
		->get();

		$response = array(
			'status' => true,
			'lists' => $lists,
			'resumes' => $resumes,
			'message' => 'Lokasi berhasil dipilih'
		);
		return Response::json($response);
	}

	public function fetchMonitoringScrap(Request $request)
      {
      	  $today     = date("Y-m-d");
          $tahun = date('Y');
          $dateto = $request->get('dateto');
          $location = $request->get('loc');
          if ($dateto != "") {
            $data = db::select("            
            SELECT
			    a.issue_location,
			    SUM( a.ListScrap ) as LScrap,
			    SUM( a.Received ) as RScrap
			FROM
			    (
			    SELECT
				    a.issue_location,
				    SUM( a.ListScrap ) as LScrap,
				    SUM( a.Received ) as RScrap
				FROM
				    (
				    SELECT
				        issue_location,
				        sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
				        0 AS Received
				    FROM
				        scrap_lists
								WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
				    GROUP BY
				        issue_location UNION ALL
				    SELECT
				        issue_location,
				        0 AS ListScrap,
				        sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
				    FROM
				        scrap_logs
								WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
				    GROUP BY
				        issue_location
				    ) a
				GROUP BY
				    a.issue_location");
          }else{
            $data = db::select("
            SELECT
			    a.issue_location,
			    SUM( a.ListScrap ) as LScrap,
			    SUM( a.Received ) as RScrap
			FROM
			    (
			    SELECT
			        issue_location,
			        sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
			        0 AS Received
			    FROM
			        scrap_lists
			        where issue_location = '".$location."'
			        and DATE_FORMAT(created_at, '%Y-%m-%d') = '".$today."'
			    GROUP BY
			        issue_location UNION ALL
			    SELECT
			        issue_location,
			        0 AS ListScrap,
			        sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
			    FROM
			        scrap_logs
			        where issue_location = '".$location."'
			        and DATE_FORMAT(created_at, '%Y-%m-%d') = '".$today."'
			    GROUP BY
			        issue_location
			    ) a
			GROUP BY
			    a.issue_location
            ");
            // dd($data);
          }

          
          $response = array(
            'status' => true,
            'datas' => $data,
            'tahun' => $tahun,
            'dateto' => $dateto,
            'location' => $location
        );

          return Response::json($response); 
      }

     public function fetchMonitoringScrapWarehouse(Request $request)
      {	  
      	  	$date_from = $request->get('date_from');
	        $date_to = $request->get('date_to');

	        if ($date_from == "") {
	             if ($date_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "LAST_DAY(NOW())";
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "'".$date_to."'";
	             }
	        }else{
	             if ($date_to == "") {
	                  $first = "'".$date_from."'";
	                  $last = "LAST_DAY(NOW())";
	             }else{
	                  $first = "'".$date_from."'";
	                  $last = "'".$date_to."'";
	             }
	        }

	        if (($date_to && $date_from) == null ) {
	        	$data = db::select("            
	            SELECT
					    a.issue_location,
					    SUM( a.ListScrap ) as LScrap,
					    SUM( a.Received ) as RScrap
					FROM
					    (
					    SELECT
					        issue_location,
					        sum( CASE WHEN remark = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
					        0 AS Received
					    FROM
					        scrap_lists
									WHERE DATE( created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )
					    GROUP BY
					        issue_location UNION ALL
					    SELECT
					        issue_location,
					        sum( CASE WHEN remark = 'received' THEN 1 ELSE 0 END ) AS Received,
									0 AS ListScrap
					    FROM
					        scrap_logs
									WHERE DATE( created_at ) >= DATE_FORMAT( NOW(), '%Y-%m-%d' )
					    GROUP BY
					        issue_location
					    ) a
					GROUP BY
					    a.issue_location");
	        }
	        else{
	        	$data = db::select("            
	            SELECT
					    a.issue_location,
					    SUM( a.ListScrap ) as LScrap,
					    SUM( a.Received ) as RScrap
					FROM
					    (
					    SELECT
					        issue_location,
					        sum( CASE WHEN remark = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
					        0 AS Received
					    FROM
					        scrap_lists
									WHERE DATE( created_at ) >= ".$first." 
									AND DATE( created_at ) <= ".$last."
					    GROUP BY
					        issue_location UNION ALL
					    SELECT
					        issue_location,
					        sum( CASE WHEN remark = 'received' THEN 1 ELSE 0 END ) AS Received,
									0 AS ListScrap
					    FROM
					        scrap_logs
									WHERE DATE( created_at ) >= ".$first." 
									AND DATE( created_at ) <= ".$last."
					    GROUP BY
					        issue_location
					    ) a
					GROUP BY
					    a.issue_location");	
	        }
	        





   //    	  $today     = date("Y-m-d");
   //        $tahun = date('Y');
   //        $dateto = $request->get('dateto');
   //        if ($dateto != "") {
   //          $data = db::select("            
   //          SELECT
			//     a.issue_location,
			//     SUM( a.ListScrap ) as LScrap,
			//     SUM( a.Received ) as RScrap
			// FROM
			//     (
			//     SELECT
			// 	    a.issue_location,
			// 	    SUM( a.ListScrap ) as LScrap,
			// 	    SUM( a.Received ) as RScrap
			// 	FROM
			// 	    (
			// 	    SELECT
			// 	        issue_location,
			// 	        sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
			// 	        0 AS Received
			// 	    FROM
			// 	        scrap_lists
			// 					WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
			// 	    GROUP BY
			// 	        issue_location UNION ALL
			// 	    SELECT
			// 	        issue_location,
			// 	        0 AS ListScrap,
			// 	        sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
			// 	    FROM
			// 	        scrap_logs
			// 					WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
			// 	    GROUP BY
			// 	        issue_location
			// 	    ) a
			// 	GROUP BY
			// 	    a.issue_location");
   //        }else{
   //          $data = db::select("
   //          SELECT
			//     a.issue_location,
			//     SUM( a.ListScrap ) as LScrap,
			//     SUM( a.Received ) as RScrap
			// FROM
			//     (
			//     SELECT
			//         issue_location,
			//         sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
			//         0 AS Received
			//     FROM
			//         scrap_lists
			//         WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$today."'
			//     GROUP BY
			//         issue_location UNION ALL
			//     SELECT
			//         issue_location,
			//         0 AS ListScrap,
			//         sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
			//     FROM
			//         scrap_logs
			//         WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$today."'
			//     GROUP BY
			//         issue_location
			//     ) a
			// GROUP BY
			//     a.issue_location
   //          ");
            // dd($data);
          // }

          
          $response = array(
            'status' => true,
            'datas' => $data,
            // 'tahun' => $tahun,
            // 'dateto' => $dateto
            'date_from' => $date_from,
            'date_to' => $date_to
        );

          return Response::json($response); 
      }

     public function fatchMonitoringDisplayScrap(Request $request)
      {
          $tahun = date('Y');
          $dateto = $request->get('dateto');

          $location = $request->get('loc');

          if ($dateto != "") {
            $data = db::select("            
            SELECT
			    a.issue_location,
			    SUM( a.ListScrap ) as LScrap,
			    SUM( a.Received ) as RScrap
			FROM
			    (
			    SELECT
				    a.issue_location,
				    SUM( a.ListScrap ) as LScrap,
				    SUM( a.Received ) as RScrap
				FROM
				    (
				    SELECT
				        issue_location,
				        sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
				        0 AS Received
				    FROM
				        scrap_lists
								WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
				    GROUP BY
				        issue_location UNION ALL
				    SELECT
				        issue_location,
				        0 AS ListScrap,
				        sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
				    FROM
				        scrap_logs
								WHERE DATE_FORMAT(created_at, '%Y-%m-%d') = '".$dateto."'
				    GROUP BY
				        issue_location
				    ) a
				GROUP BY
				    a.issue_location");
          }else{
            $data = db::select("
            SELECT
			    a.issue_location,
			    SUM( a.ListScrap ) as LScrap,
			    SUM( a.Received ) as RScrap
			FROM
			    (
			    SELECT
			        issue_location,
			        sum( CASE WHEN `remark` = 'pending' THEN 1 ELSE 0 END ) AS ListScrap,
			        0 AS Received
			    FROM
			        scrap_lists
			    GROUP BY
			        issue_location UNION ALL
			    SELECT
			        issue_location,
			        0 AS ListScrap,
			        sum( CASE WHEN `remark` = 'received' THEN 1 ELSE 0 END ) AS Received
			    FROM
			        scrap_logs
			    GROUP BY
			        issue_location
			    ) a
			GROUP BY
			    a.issue_location
            ");
          }

          
          $response = array(
            'status' => true,
            'datas' => $data,
            'tahun' => $tahun,
            'dateto' => $dateto
        );

          return Response::json($response); 
      }

    public function indexScrapRecord(){
	    $materials = db::table('scrap_materials')
		->whereNull('deleted_at')
		->select('material_number', 'material_description as description', 'issue_location')
		->orderBy('issue_location', 'ASC')
		->orderBy('material_number', 'ASC')
		->get();

		return view('scrap.scrap_record', array(
			'title' => 'Scrap Logs',
			'storage_locations' => $this->storage_location,
			'reicives' => $this->reicive,
			'categorys' => $this->category,
			'category_reasons' => $this->category_reason,
			'materials' => $materials
		))->with('page', 'Scrap Logs');
	}

	public function fetchRecord(Request $request)
	{
	    try {
        $id_user = Auth::id();
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $now = date('Y-m-d');
        $issue = $request->get('issue');
        $reicive = $request->get('reicive');
        $category = $request->get('category');
        $category_reason = $request->get('category_reason');



        if ($date_from == '') {
            if ($date_to == '') {
               $whereDate = 'DATE(created_at) BETWEEN CONCAT(DATE_FORMAT("'.$now.'" - INTERVAL 30 DAY,"%Y-%m-%d")) AND "'.$now.'"';
           }else{
               $whereDate = 'DATE(created_at) BETWEEN CONCAT(DATE_FORMAT("'.$now.'" - INTERVAL 30 DAY,"%Y-%m-%d")) AND "'.$date_to.'"';
           }
       }else{
        if ($date_to == '') {
           $whereDate = 'DATE(created_at) BETWEEN "'.$date_from.'" AND DATE(NOW())';
       }else{
           $whereDate = 'DATE(created_at) BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
       }
	   }

	    $whereIssue = "";
	    if ($request->get('issue') == "") {
	       $whereIssue = "";
	    }else{
	    $whereIssue= "AND issue_location = '".$request->get('issue')."'";
		}

		$whereReicive = "";
		if ($request->get('reicive') == "") {
		    $whereReicive = "";
		}else{
		    $whereReicive= "AND receive_location = '".$request->get('reicive')."'";
		}

		$whereCategory = "";
		if ($request->get('category') == "") {
		    $whereCategory = "";
		}else{
		    $whereCategory= "AND category = '".$request->get('category')."'";
		}

		$whereCategoryReason = "";
		if ($request->get('category_reason') == "") {
		    $whereCategoryReason = "";
		}else{
		    $whereCategoryReason= "AND category_reason = '".$request->get('category_reason')."'";
		}

		// $jenis = "";
		// if ($request->get('pekerjaan') == "" || $request->get('pekerjaan') == "All") {
		//     $jenis = "";
		// }else {
		//     $jenis = "AND jenis = '".$request->get('pekerjaan')."'";
		// }

		// $pekerjaan = db::select('SELECT * FROM warehouse_logs');
		// $pelayanan_exim = db::select('SELECT DISTINCT pelayanan_exim FROM warehouse_logs ORDER BY pelayanan_exim');

		$profession = DB::SELECT("SELECT *
		    FROM
		    `scrap_logs`
		    WHERE
		    ".$whereDate." ".$whereIssue." ".$whereReicive." ".$whereCategory." ".$whereCategoryReason."
		    ORDER BY
		    created_at DESC");

		$response = array(
		    'status' => true,
		    'profession' => $profession
		    // 'jenis' => $jenis,
		    // 'pekerjaan' => $pekerjaan,
		    // 'pelayanan_exim' => $pelayanan_exim

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
	// ==================================================================================================================
	public function createScrap()
	{
	    $title = "";
	    $title_jp = "";

	    $reason = db::select('SELECT reason, reason_name FROM scrap_reasons ORDER BY reason ASC');


		return view('scrap.create', array(
			'reason' => $reason,
			'title' => 'Scrap Material',
			'title_jp' => 'スクラップ材料',
			'storage_locations' => $this->storage_location,
			'category_reason' => $this->category_reason,
			'reicive' => $this->reicive,
			'category' => $this->category
		))->with('page', 'Scrap');
	}
}
