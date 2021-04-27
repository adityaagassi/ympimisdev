<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\User;
use App\NgList;
use App\QaMaterial;
use App\QaInspectionLevel;
use App\QaIncomingNgTemp;
use App\QaIncomingNgLog;
use App\QaIncomingLog;
use App\EmployeeSync;
use Response;
use Excel;
use DataTables;
use Carbon\Carbon;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class QualityAssuranceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }

        $this->location = ['wi1_Woodwind Instrument (WI) 1',
                      'wi2_Woodwind Instrument (WI) 2',
                      'ei_Educational Instrument (EI)',
                      'sx_Saxophone Body',
                      'cs_Case',
                      'ps_Pipe Silver',];
  	}

  	public function index()
  	{
  		return view('qa.index')
      ->with('title', 'Quality Assurance')
      ->with('title_jp', '品保')
  		->with('page', 'Quality Assurance')
  		->with('jpn', '品保');
  	}

    public function index_cpar()
    {
      return view('cpar.index_cpar')
      ->with('title', 'Quality Assurance')
      ->with('title_jp', '品保')
      ->with('page', 'Quality Assurance')
      ->with('jpn', '品保');
    }

    public function index_ymmj()
    {
      return view('qc_ymmj.index_ymmj')
      ->with('title', 'Quality Assurance')
      ->with('title_jp', '品保')
      ->with('page', 'Quality Assurance')
      ->with('jpn', '品保');
    }

  	public function indexIncomingCheck($location)
  	{
  		$inspection_level = QaInspectionLevel::get();
  		$nglists = NgList::where('location','qa-incoming')->where('remark',$location)->get();

  		if ($location == 'wi1') {
  			$loc = 'Woodwind Instrument (WI) 1';
  		}else if ($location == 'wi2') {
  			$loc = 'Woodwind Instrument (WI) 2';
  		}else if($location == 'ei'){
  			$loc = 'Educational Instrument (EI)';
  		}else if($location == 'sx'){
        $loc = 'Saxophone Body';
      }else if ($location == 'cs'){
  			$loc = 'Case';
  		}else if($location == 'ps'){
  			$loc = 'Pipe Silver';
  		}

  		$emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

      $vendor = DB::SELECT("SELECT DISTINCT
        ( vendor ) 
      FROM
        qa_materials 
      ORDER BY
        LENGTH( vendor ) ASC");

      $material = DB::SELECT("SELECT DISTINCT
        ( material_number ),
        material_description 
      FROM
        qa_materials 
      ORDER BY
        material_description ASC");

  		return view('qa.index_incoming_check')
  		->with('ng_lists', $nglists)
  		->with('inspection_level', $inspection_level)
      ->with('vendors', $vendor)
      ->with('materials', $material)
  		->with('loc', $loc)
  		->with('location', $location)
  		->with('emp', $emp)
  		->with('title', 'Incoming Check QA')
  		->with('title_jp', '受入検査品保')
  		->with('page', 'Quality Assurance')
  		->with('jpn', '品保');
  	}

  	public function fetchCheckMaterial(Request $request)
  	{
  		try {
  			$material = QaMaterial::where('material_number',$request->get('material_number'))->first();
  			if (count($material) > 0) {
  				$response = array(
	                'status' => true,
	                'material'=> $material
	            );
	            return Response::json($response);
  			}else{
  				$response = array(
	                'status' => false,
	                'message' => 'Material Tidak Ditemukan'
	            );
	            return Response::json($response);
  			}
  		} catch (\Exception $e) {
  			$response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
  		}
  	}

  	public function inputNgTemp(Request $request)
  	{
  		try {
  			$material_number = strtoupper($request->get('material_number'));
			$material_description = $request->get('material_description');
			$vendor = $request->get('vendor');
			$qty_rec = $request->get('qty_rec');
      $lot_number = $request->get('lot_number');
			$qty_check = $request->get('qty_check');
			$invoice = $request->get('invoice');
			$inspection_level = $request->get('inspection_level');
			$ng_name = $request->get('ng_name');
			$qty_ng = $request->get('qty_ng');
			$status_ng = $request->get('status_ng');
			$note_ng = $request->get('note_ng');
			$inspector = $request->get('inspector');
			$location = $request->get('location');
			$incoming_check_code = $location."_".$material_number."_".$vendor."_".$invoice."_".$inspection_level."_".$inspector;

			QaIncomingNgTemp::create([
				'incoming_check_code' => $incoming_check_code,
        'inspector_id' => $inspector,
        'location' => $location,
        'material_number' => $material_number,
        'material_description' => $material_description,
        'vendor' => $vendor,
        'qty_rec' => $qty_rec,
        'lot_number' => $lot_number,
				'qty_check' => $qty_check,
				'invoice' => $invoice,
				'inspection_level' => $inspection_level,
				'ng_name' => $ng_name,
				'qty_ng' => $qty_ng,
				'status_ng' => $status_ng,
				'note_ng' => $note_ng,
                'created_by' => Auth::id()
            ]);

  			$response = array(
                'status' => true,
                'message' => 'Input NG Berhasil',
                'incoming_check_code' => $incoming_check_code
            );
            return Response::json($response);
  		} catch (\Exception $e) {
  			$response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
  		}
  	}

  	public function fetchNgTemp(Request $request)
  	{
  		try {
  			if ($request->get('incoming_check_code') != "") {
  				$ng_temp = QaIncomingNgTemp::where('incoming_check_code',$request->get('incoming_check_code'))->get();
  				$response = array(
	                'status' => true,
	                'incoming_check_code' => $request->get('incoming_check_code'),
	                'ng_temp' => $ng_temp
	            );
	            return Response::json($response);
  			}else{
  				$response = array(
	                'status' => true,

	            );
	            return Response::json($response);
  			}
  		} catch (\Exception $e) {
  			$response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
  		}
  	}

  	public function fetchNgList(Request $request)
  	{
  		try {
  			$ng_list = NgList::where('location','qa-incoming')->where('remark',$request->get('location'))->get();

  			$response = array(
                'status' => true,
                'ng_list' => $ng_list
            );
            return Response::json($response);
  		} catch (\Exception $e) {
  			$response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
  		}
  	}

  	public function deleteNgTemp(Request $request)
  	{
  		try {
  			$delete = QaIncomingNgTemp::where('id',$request->get('id'))->forceDelete();
  			$response = array(
                'status' => true,
                'message' => 'Success Delete NG'
            );
            return Response::json($response);
  		} catch (\Exception $e) {
  			$response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
  		}
  	}

  	public function inputNgLog(Request $request)
  	{
  		try {
  			$material_number = strtoupper($request->get('material_number'));
  			$material_description = $request->get('material_description');
  			$vendor = $request->get('vendor');
        $lot_number = $request->get('lot_number');
  			$qty_rec = $request->get('qty_rec');
  			$qty_check = $request->get('qty_check');
  			$invoice = $request->get('invoice');
  			$inspection_level = $request->get('inspection_level');
  			$inspector = $request->get('inspector');
  			$location = $request->get('location');
  			$incoming_check_code = $request->get('incoming_check_code')."_".date('Y-m-d H:i:s');
  			$repair = $request->get('repair');
  			$scrap = $request->get('scrap');
  			$return = $request->get('returns');
  			$total_ok = $request->get('total_ok');
  			$total_ng = $request->get('total_ng');
  			$ng_ratio = $request->get('ng_ratio');
  			$status_lot = $request->get('status_lot');

			 $log = QaIncomingLog::create([
  	        'incoming_check_code' => $incoming_check_code,
            'inspector_id' => $inspector,
            'location' => $location,
            'lot_number' => $lot_number,
            'material_number' => $material_number,
            'material_description' => $material_description,
            'vendor' => $vendor,
            'qty_rec' => $qty_rec,
    				'qty_check' => $qty_check,
    				'invoice' => $invoice,
    				'inspection_level' => $inspection_level,
    				'repair' => $repair,
    				'scrap' => $scrap,
    				'return' => $return,
    				'total_ok' => $total_ok,
    				'total_ng' => $total_ng,
    				'ng_ratio' => $ng_ratio,
    				'status_lot' => $status_lot,
            'created_by' => Auth::id()
        ]);

        $ng_temp = QaIncomingNgTemp::where('incoming_check_code',$request->get('incoming_check_code'))->get();

        foreach ($ng_temp as $key) {
        	QaIncomingNgLog::create([
			         'incoming_check_code' => $incoming_check_code,
               'incoming_check_log_id' => $log->id,
              'inspector_id' => $inspector,
              'location' => $key->location,
              'lot_number' => $lot_number,
              'material_number' => $key->material_number,
              'material_description' => $key->material_description,
              'vendor' => $key->vendor,
              'qty_rec' => $key->qty_rec,
    					'qty_check' => $key->qty_check,
    					'invoice' => $key->invoice,
    					'inspection_level' => $key->inspection_level,
    					'ng_name' => $key->ng_name,
    					'qty_ng' => $key->qty_ng,
    					'status_ng' => $key->status_ng,
    					'note_ng' => $key->note_ng,
              'created_by' => Auth::id()
          ]);
          QaIncomingNgTemp::where('id',$key->id)->forceDelete();
        }

        $response = array(
            'status' => true,
            'message' => 'Success Input Incoming Check'
        );
        return Response::json($response);
  		} catch (\Exception $e) {
  			$response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
  		}
  	}

    public function indexDisplayIncomingLotStatus()
    {
      return view('qa.index_lot_monitoring')
      ->with('title', 'QA Incoming Check Material Lot Out Monitoring')
      ->with('title_jp', 'QA受入検査ロットアウト品の表示')
      ->with('location', $this->location)
      ->with('page', 'QA Incoming Check Material Lot Out Monitoring')
      ->with('jpn', 'QA受入検査ロットアウト品の表示');
    }

    public function fetchDisplayIncomingLotStatus(Request $request)
    {
      try {

        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "DATE(NOW())";
                  $last = "DATE(NOW())";
                  $date = date('Y-m-d');
                  $monthTitle = date("d M Y", strtotime($date));
             }else{
                  $first = "DATE(NOW())";
                  $last = "'".$date_to."'";
                  $date = date('Y-m-d');
                  $monthTitle = date("d M Y", strtotime($date)).' to '.date("d M Y", strtotime($date_to));
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "DATE(NOW())";
                  $date = date('Y-m-d');
                  $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date));
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
                  $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date_to));
             }
        }

        $lot_count = DB::SELECT("SELECT DISTINCT
          ( remark ) AS location,
          (
          SELECT
            count( id ) 
          FROM
            qa_incoming_logs 
          WHERE
            status_lot = 'Lot OK' 
            AND DATE( created_at ) BETWEEN ".$first." and ".$last."
            AND qa_incoming_logs.location = ng_lists.remark 
          ) AS lot_ok,
          (
          SELECT
            count( id ) 
          FROM
            qa_incoming_logs 
          WHERE
            status_lot = 'Lot OUT' 
            AND DATE( created_at ) BETWEEN ".$first." and ".$last."
            AND qa_incoming_logs.location = ng_lists.remark 
          ) AS lot_out 
        FROM
          ng_lists 
        WHERE
          location = 'qa-incoming'");

        $lot_detail = DB::SELECT("SELECT
        *,
        DATE( qa_incoming_logs.created_at ) AS date_lot,
        ( SELECT GROUP_CONCAT( ng_name ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
         ( SELECT DISTINCT(vendor_shortname) FROM qa_materials WHERE qa_materials.vendor = qa_incoming_logs.vendor ) AS vendor_shortname 
      FROM
        qa_incoming_logs 
        left join employee_syncs on employee_syncs.employee_id = qa_incoming_logs.inspector_id
      WHERE
        qa_incoming_logs.status_lot = 'Lot Out' 
        AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first." and ".$last."
      ORDER BY
        qa_incoming_logs.created_at DESC");

        $response = array(
            'status' => true,
            'lot_count' => $lot_count,
            'monthTitle' => $monthTitle,
            'lot_detail' => $lot_detail,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function indexDisplayIncomingMaterialDefect()
    {
      $vendor = DB::SELECT("SELECT DISTINCT
        ( vendor ) 
      FROM
        qa_materials 
      ORDER BY
        LENGTH( vendor ) ASC");

      $material = DB::SELECT("SELECT DISTINCT
        ( material_number ),
        material_description 
      FROM
        qa_materials 
      ORDER BY
        material_description ASC");

      return view('qa.index_material_defect')
      ->with('title', 'QA Pareto Defect Incoming')
      ->with('title_jp', 'QA受入パレット不良')
      ->with('location', $this->location)
      ->with('materials', $material)
      ->with('vendors', $vendor)
      ->with('page', 'QA Pareto Defect Incoming')
      ->with('jpn', 'QA受入パレット不良');
    }

    public function fetchDisplayIncomingMaterialDefect(Request $request)
    {
      try {

        $month_from = $request->get('month_from');
        $month_to = $request->get('month_to');
        if ($month_from == "") {
             if ($month_to == "") {
                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
             }else{
                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
                  $last = "'".$month_to."'";
             }
        }else{
             if ($month_to == "") {
                  $first = "'".$month_from."'";
                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
             }else{
                  $first = "'".$month_from."'";
                  $last = "'".$month_to."'";
             }
        }

        $vendor = '';
        if($request->get('vendor') != null){
          $vendors =  explode(",", $request->get('vendor'));
          for ($i=0; $i < count($vendors); $i++) {
            $vendor = $vendor."'".$vendors[$i]."'";
            if($i != (count($vendors)-1)){
              $vendor = $vendor.',';
            }
          }
          $vendorin = " and `vendor` in (".$vendor.") ";
        }
        else{
          $vendorin = "";
        }

        $material = '';
        if($request->get('material') != null){
          $materials =  explode(",", $request->get('material'));
          for ($i=0; $i < count($materials); $i++) {
            $material = $material."'".$materials[$i]."'";
            if($i != (count($materials)-1)){
              $material = $material.',';
            }
          }
          $materialin = " and `material_number` in (".$material.") ";
        }
        else{
          $materialin = "";
        }

        $material_defect = DB::SELECT("SELECT
            ng_name,
            SUM( qty_ng ) AS count,
            ( SELECT SUM( qty_check ) FROM qa_incoming_ng_logs WHERE DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin." ) AS total_check 
          FROM
            qa_incoming_ng_logs 
          WHERE
            DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." 
            ".$vendorin."
            ".$materialin."
          GROUP BY
            ng_name
          ORDER BY
            count DESC");

        $material_status = DB::SELECT("SELECT
            SUM( a.total ) AS total,
            SUM( a.returnes ) AS `return`,
            SUM( a.scrapes ) AS `scrap`,
            SUM( a.repaires ) AS `repair` 
          FROM
            (
            SELECT
              SUM( qty_check ) AS total,
              0 AS returnes,
              0 AS scrapes,
              0 AS repaires 
            FROM
              qa_incoming_logs 
            WHERE
              DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin." UNION ALL
            SELECT
              0 total,
              SUM( `return` ) AS returnes,
              0 AS scrapes,
              0 AS repaires 
            FROM
              qa_incoming_logs 
            WHERE
              DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin." UNION ALL
            SELECT
              0 total,
              0 AS returnes,
              SUM( scrap ) AS scrapes,
              0 AS repaires 
            FROM
              qa_incoming_logs 
            WHERE
              DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin." UNION ALL
            SELECT
              0 total,
              0 AS returnes,
              0 AS scrapes,
              SUM( `repair` ) AS repaires 
            FROM
              qa_incoming_logs 
          WHERE
            DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin.") a");

        $response = array(
            'status' => true,
            'material_defect' => $material_defect,
            'material_status' => $material_status,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function fetchDisplayIncomingMaterialDefectDetail(Request $request)
    {
      try {
        $month_from = $request->get('month_from');
        $month_to = $request->get('month_to');
        if ($month_from == "") {
             if ($month_to == "") {
                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
             }else{
                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
                  $last = "'".$month_to."'";
             }
        }else{
             if ($month_to == "") {
                  $first = "'".$month_from."'";
                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
             }else{
                  $first = "'".$month_from."'";
                  $last = "'".$month_to."'";
             }
        }

        $vendor = '';
        if($request->get('vendor') != null){
          $vendors =  explode(",", $request->get('vendor'));
          for ($i=0; $i < count($vendors); $i++) {
            $vendor = $vendor."'".$vendors[$i]."'";
            if($i != (count($vendors)-1)){
              $vendor = $vendor.',';
            }
          }
          $vendorin = " and `vendor` in (".$vendor.") ";
        }
        else{
          $vendorin = "";
        }

        $material = '';
        if($request->get('material') != null){
          $materials =  explode(",", $request->get('material'));
          for ($i=0; $i < count($materials); $i++) {
            $material = $material."'".$materials[$i]."'";
            if($i != (count($materials)-1)){
              $material = $material.',';
            }
          }
          $materialin = " and `material_number` in (".$material.") ";
        }
        else{
          $materialin = "";
        }

        $detail = DB::SELECT("SELECT
            *,
          date(created_at) as created
          FROM
            qa_incoming_ng_logs 
          WHERE
            DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first." 
            AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last." ".$vendorin." ".$materialin." 
            AND ng_name = '".$request->get('categories')."'");

        $response = array(
            'status' => true,
            'detail' => $detail,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function indexDisplayIncomingNgRate()
    {
      $vendor = DB::SELECT("SELECT DISTINCT
        ( vendor ) 
      FROM
        qa_materials 
      ORDER BY
        LENGTH( vendor ) ASC");

      $material = DB::SELECT("SELECT DISTINCT
        ( material_number ),
        material_description 
      FROM
        qa_materials 
      ORDER BY
        material_description ASC");

      return view('qa.index_ng_rate')
      ->with('title', 'Daily NG Rate Incoming Check QA')
      ->with('title_jp', '日次QA受入検査の不良率')
      ->with('location', $this->location)
      ->with('materials', $material)
      ->with('vendors', $vendor)
      ->with('page', 'Daily NG Rate Incoming Check QA')
      ->with('jpn', '日次QA受入検査の不良率');
    }

    public function fetchDisplayIncomingNgRate(Request $request)
    {
      try {

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

        $vendor = '';
        if($request->get('vendor') != null){
          $vendors =  explode(",", $request->get('vendor'));
          for ($i=0; $i < count($vendors); $i++) {
            $vendor = $vendor."'".$vendors[$i]."'";
            if($i != (count($vendors)-1)){
              $vendor = $vendor.',';
            }
          }
          $vendorin = " and `vendor` in (".$vendor.") ";
        }
        else{
          $vendorin = "";
        }

        $material = '';
        if($request->get('material') != null){
          $materials =  explode(",", $request->get('material'));
          for ($i=0; $i < count($materials); $i++) {
            $material = $material."'".$materials[$i]."'";
            if($i != (count($materials)-1)){
              $material = $material.',';
            }
          }
          $materialin = " and `material_number` in (".$material.") ";
        }
        else{
          $materialin = "";
        }

        $ng_rate = DB::SELECT("SELECT
          DATE( created_at ) AS months,
          SUM( qty_check ) AS checkes,
          SUM( `return` ) AS returnes,
          SUM( `repair` ) AS repaires,
          ROUND((( SUM( `repair` )+ SUM( `return` ))/ SUM( qty_check )) * 100, 1 ) AS persen 
        FROM
          `qa_incoming_logs` 
        WHERE
          DATE( created_at ) >= ".$first."
          AND DATE( created_at ) <= ".$last."
          ".$vendorin." ".$materialin."
        GROUP BY
          DATE(
          created_at)");

        $response = array(
            'status' => true,
            'ng_rate' => $ng_rate,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function fetchDisplayIncomingNgRateDetail(Request $request)
    {
      try {

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

        $vendor = '';
        if($request->get('vendor') != null){
          $vendors =  explode(",", $request->get('vendor'));
          for ($i=0; $i < count($vendors); $i++) {
            $vendor = $vendor."'".$vendors[$i]."'";
            if($i != (count($vendors)-1)){
              $vendor = $vendor.',';
            }
          }
          $vendorin = " and `vendor` in (".$vendor.") ";
        }
        else{
          $vendorin = "";
        }

        $material = '';
        if($request->get('material') != null){
          $materials =  explode(",", $request->get('material'));
          for ($i=0; $i < count($materials); $i++) {
            $material = $material."'".$materials[$i]."'";
            if($i != (count($materials)-1)){
              $material = $material.',';
            }
          }
          $materialin = " and `material_number` in (".$material.") ";
        }
        else{
          $materialin = "";
        }

        $detail = DB::SELECT("SELECT
          *,DATE(created_at) as created
        FROM
          `qa_incoming_logs` 
        WHERE
          DATE( created_at ) = '".$request->get('categories')."'
          ".$vendorin." ".$materialin."");

        $response = array(
            'status' => true,
            'detail' => $detail,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function indexReportIncomingCheck()
    {
      $vendor = DB::SELECT("SELECT DISTINCT
        ( vendor ) 
      FROM
        qa_materials 
      ORDER BY
        LENGTH( vendor ) ASC");

      $material = DB::SELECT("SELECT DISTINCT
        ( material_number ),
        material_description 
      FROM
        qa_materials 
      ORDER BY
        material_description ASC");

      $inspection_level = DB::SELECT("SELECT * FROM `ympimis`.`qa_inspection_levels`");

      return view('qa.report_incoming_check')
      ->with('title', 'Report Incoming Check QA')
      ->with('title_jp', 'QA受入検査の報告')
      ->with('location', $this->location)
      ->with('materials', $material)
      ->with('inspection_levels', $inspection_level)
      ->with('vendors', $vendor)
      ->with('page', 'Report Incoming Check QA')
      ->with('jpn', 'QA受入検査の報告');
    }

    public function fetchReportIncomingCheck(Request $request)
    {
      try {
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

        $vendor = '';
        if($request->get('vendor') != null){
          $vendors =  explode(",", $request->get('vendor'));
          for ($i=0; $i < count($vendors); $i++) {
            $vendor = $vendor."'".$vendors[$i]."'";
            if($i != (count($vendors)-1)){
              $vendor = $vendor.',';
            }
          }
          $vendorin = " and `vendor` in (".$vendor.") ";
        }
        else{
          $vendorin = "";
        }

        $material = '';
        if($request->get('material') != null){
          $materials =  explode(",", $request->get('material'));
          for ($i=0; $i < count($materials); $i++) {
            $material = $material."'".$materials[$i]."'";
            if($i != (count($materials)-1)){
              $material = $material.',';
            }
          }
          $materialin = " and `material_number` in (".$material.") ";
        }
        else{
          $materialin = "";
        }

        $location = '';
        if($request->get('location') != null){
          $locations =  explode(",", $request->get('location'));
          for ($i=0; $i < count($locations); $i++) {
            $location = $location."'".$locations[$i]."'";
            if($i != (count($locations)-1)){
              $location = $location.',';
            }
          }
          $locationin = " and `location` in (".$location.") ";
        }
        else{
          $locationin = "";
        }

        $inspection_level = '';
        if($request->get('inspection_level') != null){
          $inspection_levels =  explode(",", $request->get('inspection_level'));
          for ($i=0; $i < count($inspection_levels); $i++) {
            $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
            if($i != (count($inspection_levels)-1)){
              $inspection_level = $inspection_level.',';
            }
          }
          $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
        }
        else{
          $inspection_levelin = "";
        }

        $datas = DB::SELECT("SELECT
          qa_incoming_logs.id as id_log,
          qa_incoming_logs.location,
          employee_syncs.employee_id,
          employee_syncs.name,
          qa_incoming_logs.lot_number,
          qa_incoming_logs.material_number,
          qa_incoming_logs.material_description,
          qa_incoming_logs.vendor,
          qa_incoming_logs.invoice,
          qa_incoming_logs.inspection_level,
          qa_incoming_logs.`repair`,
          qa_incoming_logs.`return`,
          qa_incoming_logs.`qty_rec`,
          qa_incoming_logs.`qty_check`,
          qa_incoming_logs.`total_ok`,
          qa_incoming_logs.`total_ng`,
          qa_incoming_logs.`ng_ratio`,
          qa_incoming_logs.`status_lot`,
          DATE( qa_incoming_logs.created_at ) AS created,
          ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
          ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
          ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
          ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
        FROM
          qa_incoming_logs
          JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
        WHERE
          DATE( qa_incoming_logs.created_at ) >= ".$first." 
          AND DATE( qa_incoming_logs.created_at ) <= ".$last."
          ".$locationin." ".$inspection_levelin." ".$materialin." ".$vendorin." ");

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function indexReportLotOut()
    {
      $vendor = DB::SELECT("SELECT DISTINCT
        ( vendor ) 
      FROM
        qa_materials 
      ORDER BY
        LENGTH( vendor ) ASC");

      $material = DB::SELECT("SELECT DISTINCT
        ( material_number ),
        material_description 
      FROM
        qa_materials 
      ORDER BY
        material_description ASC");

      $inspection_level = DB::SELECT("SELECT * FROM `ympimis`.`qa_inspection_levels`");

      return view('qa.report_lot_out')
      ->with('title', 'Report Lot Out Incoming Check QA')
      ->with('title_jp', 'QA受入検査ロットアウトの報告')
      ->with('location', $this->location)
      ->with('materials', $material)
      ->with('inspection_levels', $inspection_level)
      ->with('vendors', $vendor)
      ->with('page', 'Report Lot Out Incoming Check QA')
      ->with('jpn', 'QA受入検査ロットアウトの報告');
    }

    public function fetchReportLotOut(Request $request)
    {
      try {
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

        $vendor = '';
        if($request->get('vendor') != null){
          $vendors =  explode(",", $request->get('vendor'));
          for ($i=0; $i < count($vendors); $i++) {
            $vendor = $vendor."'".$vendors[$i]."'";
            if($i != (count($vendors)-1)){
              $vendor = $vendor.',';
            }
          }
          $vendorin = " and `vendor` in (".$vendor.") ";
        }
        else{
          $vendorin = "";
        }

        $material = '';
        if($request->get('material') != null){
          $materials =  explode(",", $request->get('material'));
          for ($i=0; $i < count($materials); $i++) {
            $material = $material."'".$materials[$i]."'";
            if($i != (count($materials)-1)){
              $material = $material.',';
            }
          }
          $materialin = " and `material_number` in (".$material.") ";
        }
        else{
          $materialin = "";
        }

        $location = '';
        if($request->get('location') != null){
          $locations =  explode(",", $request->get('location'));
          for ($i=0; $i < count($locations); $i++) {
            $location = $location."'".$locations[$i]."'";
            if($i != (count($locations)-1)){
              $location = $location.',';
            }
          }
          $locationin = " and `location` in (".$location.") ";
        }
        else{
          $locationin = "";
        }

        $inspection_level = '';
        if($request->get('inspection_level') != null){
          $inspection_levels =  explode(",", $request->get('inspection_level'));
          for ($i=0; $i < count($inspection_levels); $i++) {
            $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
            if($i != (count($inspection_levels)-1)){
              $inspection_level = $inspection_level.',';
            }
          }
          $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
        }
        else{
          $inspection_levelin = "";
        }

        $datas = DB::SELECT("SELECT
          qa_incoming_logs.id as id_log,
          qa_incoming_logs.location,
          employee_syncs.employee_id,
          employee_syncs.name,
          qa_incoming_logs.material_number,
          qa_incoming_logs.lot_number,
          qa_incoming_logs.material_description,
          qa_incoming_logs.vendor,
          qa_incoming_logs.invoice,
          qa_incoming_logs.inspection_level,
          qa_incoming_logs.`repair`,
          qa_incoming_logs.`return`,
          qa_incoming_logs.`qty_rec`,
          qa_incoming_logs.`qty_check`,
          qa_incoming_logs.`total_ok`,
          qa_incoming_logs.`total_ng`,
          qa_incoming_logs.`ng_ratio`,
          qa_incoming_logs.`status_lot`,
          qa_incoming_logs.`report_evidence`,
          qa_incoming_logs.`send_email_status`,
          qa_incoming_logs.`send_email_at`,
          DATE( qa_incoming_logs.created_at ) AS created,
          ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
          ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
          ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
          ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
        FROM
          qa_incoming_logs
          JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
        WHERE
          DATE( qa_incoming_logs.created_at ) >= ".$first." 
          AND DATE( qa_incoming_logs.created_at ) <= ".$last."
          AND status_lot = 'Lot Out'
          ".$locationin." ".$inspection_levelin." ".$materialin." ".$vendorin." ");

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function inputReportLotOut(Request $request)
    {
      try {
        $id_log = $request->get('id_log');
        $report_evidence = $request->get('report_evidence');
        $log = QaIncomingLog::where('id',$id_log)->first();
        $log->report_evidence = $report_evidence;
        $log->save();

        $response = array(
            'status' => true,
            'message' => 'Success Input Evidence'
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function excelReportIncomingCheck(Request $request)
    {
      try {
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

        $vendor = '';
        if($request->get('vendor') != null){
          $vendors =  explode(",", $request->get('vendor'));
          for ($i=0; $i < count($vendors); $i++) {
            $vendor = $vendor."'".$vendors[$i]."'";
            if($i != (count($vendors)-1)){
              $vendor = $vendor.',';
            }
          }
          $vendorin = " and `vendor` in (".$vendor.") ";
        }
        else{
          $vendorin = "";
        }

        $material = '';
        if($request->get('material') != null){
          $materials =  explode(",", $request->get('material'));
          for ($i=0; $i < count($materials); $i++) {
            $material = $material."'".$materials[$i]."'";
            if($i != (count($materials)-1)){
              $material = $material.',';
            }
          }
          $materialin = " and `material_number` in (".$material.") ";
        }
        else{
          $materialin = "";
        }

        $location = '';
        if($request->get('location') != null){
          $locations =  explode(",", $request->get('location'));
          for ($i=0; $i < count($locations); $i++) {
            $location = $location."'".$locations[$i]."'";
            if($i != (count($locations)-1)){
              $location = $location.',';
            }
          }
          $locationin = " and `location` in (".$location.") ";
        }
        else{
          $locationin = "";
        }

        $inspection_level = '';
        if($request->get('inspection_level') != null){
          $inspection_levels =  explode(",", $request->get('inspection_level'));
          for ($i=0; $i < count($inspection_levels); $i++) {
            $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
            if($i != (count($inspection_levels)-1)){
              $inspection_level = $inspection_level.',';
            }
          }
          $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
        }
        else{
          $inspection_levelin = "";
        }
        // var_dump($data);
        $datas = DB::SELECT("SELECT
          qa_incoming_logs.location,
          employee_syncs.employee_id,
          employee_syncs.name,
          qa_incoming_logs.lot_number,
          qa_incoming_logs.material_number,
          qa_incoming_logs.material_description,
          qa_incoming_logs.vendor,
          qa_incoming_logs.invoice,
          qa_incoming_logs.inspection_level,
          qa_incoming_logs.`repair`,
          qa_incoming_logs.`return`,
          qa_incoming_logs.`qty_rec`,
          qa_incoming_logs.`qty_check`,
          qa_incoming_logs.`total_ok`,
          qa_incoming_logs.`total_ng`,
          qa_incoming_logs.`ng_ratio`,
          qa_incoming_logs.`status_lot`,
          DATE( qa_incoming_logs.created_at ) AS created,
          ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
          ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
          ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
          ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
        FROM
          qa_incoming_logs
          JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
        WHERE
          DATE( qa_incoming_logs.created_at ) >= ".$first." 
          AND DATE( qa_incoming_logs.created_at ) <= ".$last."
          ".$locationin." ".$inspection_levelin." ".$materialin." ".$vendorin." 
          ");

        $data = array(
        'datas' => $datas
        );

           ob_clean();
        Excel::create('Incoming Check QA Report', function($excel) use ($data){
          $excel->sheet('Incoming Check QA', function($sheet) use ($data) {
            return $sheet->loadView('qa.excel_incoming_check', $data);
          });
        })->export('xlsx');

        return redirect()->route('report_incoming_qa')->with('status','Success Export Data');
      } catch (\Exception $e) {
        return redirect()->route('report_incoming_qa')->with('error','Failed Export Data');
      }
    }

    public function fetchDetailRecord(Request $request)
    {
      try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "DATE_FORMAT( NOW() - INTERVAL 7 DAY, '%Y-%m-%d' ) ";
                  $last = "DATE(NOW())";
             }else{
                  $first = "DATE_FORMAT( NOW() - INTERVAL 7 DAY, '%Y-%m-%d' ) ";
                  $last = "'".$date_to."'";
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "DATE(NOW())";
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
             }
        }

        $vendor = '';
        if($request->get('vendor') != null){
          $vendors =  explode(",", $request->get('vendor'));
          for ($i=0; $i < count($vendors); $i++) {
            $vendor = $vendor."'".$vendors[$i]."'";
            if($i != (count($vendors)-1)){
              $vendor = $vendor.',';
            }
          }
          $vendorin = " and `vendor` in (".$vendor.") ";
        }
        else{
          $vendorin = "";
        }

        $material = '';
        if($request->get('material') != null){
          $materials =  explode(",", $request->get('material'));
          for ($i=0; $i < count($materials); $i++) {
            $material = $material."'".$materials[$i]."'";
            if($i != (count($materials)-1)){
              $material = $material.',';
            }
          }
          $materialin = " and `material_number` in (".$material.") ";
        }
        else{
          $materialin = "";
        }
        $detail = DB::SELECT("
          SELECT
          qa_incoming_logs.location,
          qa_incoming_logs.lot_number,
          employee_syncs.employee_id,
          employee_syncs.name,
          qa_incoming_logs.material_number,
          qa_incoming_logs.material_description,
          qa_incoming_logs.vendor,
          qa_incoming_logs.invoice,
          qa_incoming_logs.inspection_level,
          qa_incoming_logs.`repair`,
          qa_incoming_logs.`return`,
          qa_incoming_logs.`qty_rec`,
          qa_incoming_logs.`qty_check`,
          qa_incoming_logs.`total_ok`,
          qa_incoming_logs.`total_ng`,
          qa_incoming_logs.`ng_ratio`,
          qa_incoming_logs.`status_lot`,
          DATE( qa_incoming_logs.created_at ) AS created,
          ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
          ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
          ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
          ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
        FROM
          qa_incoming_logs
          JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
        WHERE
          DATE( qa_incoming_logs.created_at ) >= ".$first."
          AND DATE( qa_incoming_logs.created_at ) <= ".$last."
          ".$materialin." ".$vendorin."
        ORDER BY
          qa_incoming_logs.material_number,
          qa_incoming_logs.created_at desc,
          qa_incoming_logs.lot_number");

        $response = array(
            'status' => true,
            'detail' => $detail,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function fetchReportIncomingCheckEdit(Request $request)
    {
      try {
        $datas = DB::SELECT("SELECT
          qa_incoming_logs.id as id_log,
          qa_incoming_logs.incoming_check_code,
          qa_incoming_logs.location,
          qa_incoming_logs.lot_number,
          employee_syncs.employee_id,
          employee_syncs.name,
          qa_incoming_logs.material_number,
          qa_incoming_logs.material_description,
          qa_incoming_logs.vendor,
          qa_incoming_logs.invoice,
          qa_incoming_logs.inspection_level,
          qa_incoming_logs.`repair`,
          DATE(qa_incoming_logs.created_at) as date,
          qa_incoming_logs.`return`,
          qa_incoming_logs.`qty_rec`,
          qa_incoming_logs.`qty_check`,
          qa_incoming_logs.`total_ok`,
          qa_incoming_logs.`total_ng`,
          qa_incoming_logs.`ng_ratio`,
          qa_incoming_logs.`status_lot`,
          qa_incoming_logs.`report_evidence`,
          DATE( qa_incoming_logs.created_at ) AS created,
          ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
          ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
          ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
          ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
        FROM
          qa_incoming_logs
          JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
        WHERE
          qa_incoming_logs.id = '".$request->get('id')."'
        ORDER BY
          qa_incoming_logs.material_number,
          qa_incoming_logs.created_at desc,
          qa_incoming_logs.lot_number");

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function updateReportIncomingCheck(Request $request)
    {
      try {
        $material = $request->get('material');
        $invoice = $request->get('invoice');
        $inspection_level = $request->get('inspection_level');
        $lot_number = $request->get('lot_number');
        $qty_rec = $request->get('qty_rec');
        $status_lot = $request->get('status_lot');
        $incoming_check_code = $request->get('incoming_check_code');
        $id_log = $request->get('id_log');

        $log = QaIncomingLog::where('id',$id_log)->first();
        $materials = QaMaterial::where('material_number',$material)->first();
        $log->material_number = $materials->material_number;
        $log->material_description = $materials->material_description;
        $log->vendor = $materials->vendor;
        $log->lot_number = $lot_number;
        $log->invoice = $invoice;
        $log->inspection_level = $inspection_level;
        $log->qty_rec = $qty_rec;
        $log->status_lot = $status_lot;

        $ng_log = QaIncomingNgLog::where('incoming_check_log_id',$id_log)->get();
        if (count($ng_log) > 0) {
          foreach($ng_log as $ng_logs){
            $nglogs = QaIncomingNgLog::where('id',$ng_logs->id)->first();
            $nglogs->material_number = $materials->material_number;
            $nglogs->material_description = $materials->material_description;
            $nglogs->vendor = $materials->vendor;
            $nglogs->lot_number = $lot_number;
            $nglogs->invoice = $invoice;
            $nglogs->inspection_level = $inspection_level;
            $nglogs->qty_rec = $qty_rec;
            $nglogs->save();
          }
        }

        $log->save();

        $response = array(
            'status' => true,
            'message' => 'Success Update Data'
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function deleteReportIncomingCheck(Request $request)
    {
      try {
        $log = QaIncomingLog::where('id',$request->get('id'))->forceDelete();
        $ng_log = QaIncomingNgLog::where('incoming_check_log_id',$request->get('id'))->get();
        if (count($ng_log) > 0) {
          foreach ($ng_log as $key) {
            QaIncomingNgLog::where('id',$key->id)->forceDelete();
          }
        }

        $response = array(
            'status' => true,
            'message' => 'Success Delete Data'
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function sendReportLotOut(Request $request)
    {
      try {
        $id = $request->get('id');
        $log = QaIncomingLog::where('qa_incoming_logs.id',$id)->first();
        $log->send_email_status = 'Sent';
        $log->send_email_at = date('Y-m-d H:i:s');

        $datas = DB::SELECT("SELECT
          qa_incoming_logs.id as id_log,
          qa_incoming_logs.incoming_check_code,
          qa_incoming_logs.location,
          qa_incoming_logs.lot_number,
          employee_syncs.employee_id,
          employee_syncs.name,
          qa_incoming_logs.material_number,
          qa_incoming_logs.material_description,
          qa_incoming_logs.vendor,
          qa_incoming_logs.invoice,
          qa_incoming_logs.inspection_level,
          qa_incoming_logs.`repair`,
          DATE(qa_incoming_logs.created_at) as date,
          qa_incoming_logs.`return`,
          qa_incoming_logs.`qty_rec`,
          qa_incoming_logs.`qty_check`,
          qa_incoming_logs.`total_ok`,
          qa_incoming_logs.`total_ng`,
          qa_incoming_logs.`ng_ratio`,
          qa_incoming_logs.`status_lot`,
          qa_incoming_logs.`report_evidence`,
          DATE( qa_incoming_logs.created_at ) AS created,
          ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
          ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
          ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
          ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
        FROM
          qa_incoming_logs
          JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
        WHERE
          qa_incoming_logs.id = '".$request->get('id')."'
        ORDER BY
          qa_incoming_logs.material_number,
          qa_incoming_logs.created_at desc,
          qa_incoming_logs.lot_number");

        $mailto = QaMaterial::select('email')->where('material_number',$log->material_number)->first();
        $mail_to = $mailto->email;

        $cc = [];
        $cc[0] = 'nasiqul.ibat@music.yamaha.com';
        // $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
        // $cc[1] = 'agustina.hayati@music.yamaha.com';
        // $cc[2] = 'ratri.sulistyorini@music.yamaha.com';
        // $cc[3] = 'abdissalam.saidi@music.yamaha.com';

        $bcc = [];
        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';

        Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($datas, 'qa_incoming_check'));

        $log->save();

        $response = array(
            'status' => true,
            'message' => 'Email Berhasil Terkirim'
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }
}
