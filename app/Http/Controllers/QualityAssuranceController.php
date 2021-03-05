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
use Response;
use DataTables;
use Carbon\Carbon;

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
  	}

  	public function index()
  	{
  		return view('qa.index')
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
  		}else if ($location == 'cs'){
  			$loc = 'Case';
  		}else if($location == 'ps'){
  			$loc = 'Pipe Silver';
  		}

  		return view('qa.index_incoming_check')
  		->with('ng_lists', $nglists)
  		->with('inspection_level', $inspection_level)
  		->with('loc', $loc)
  		->with('location', $location)
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
  			$material_number = $request->get('material_number');
			$material_description = $request->get('material_description');
			$vendor = $request->get('vendor');
			$qty_rec = $request->get('qty_rec');
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
}
