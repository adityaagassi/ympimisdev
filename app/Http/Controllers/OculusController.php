<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\Employee;
use App\EmployeeSync;
use App\CodeGenerator;
use App\User;
use App\OculusUser;
use App\OculusResult;
use Auth;
use DataTables;
use Response;
use Illuminate\Support\Facades\DB;

class OculusController extends Controller
{
    function __construct()
    {
    	// $this->middleware('auth');
    }

    public function indexAuth($employee_id)
    {
    	try {
    		$emp = DB::SELECT("SELECT * from oculus_users where employee_id = '".$employee_id."'");
    		if (count($emp) > 0) {
    			$response = array(
					'status' => true,
					'message' => 'Success',
					'emp' => $emp
				);
				return Response::json($response);
    		}else{
    			$response = array(
					'status' => false,
					'message' => 'Failed'
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

    public function indexResult($employee_id,$answer,$sub_answer,$result)
    {
    	try {
    		$emp = DB::SELECT("SELECT * from oculus_users where employee_id = '".$employee_id."'");
    		if (count($emp) > 0) {
    			$oculus_result = new OculusResult([
					'employee_id' => $employee_id,
					'oculus_answer' => $answer,
					'oculus_sub_answer' => $sub_answer,
					'oculus_result' => $result,
					'created_by' => 1,
				]);
				$oculus_result->save();

	    		$response = array(
					'status' => true,
					'message' => 'Success',
					// 'emp' => $emp
				);
				return Response::json($response);
    		}else{
    			$response = array(
					'status' => false,
					'message' => 'Failed'
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

    public function fetchResult($employee_id)
    {
    	try {
    		$emp = DB::SELECT("SELECT * from oculus_users where employee_id = '".$employee_id."'");
    		if (count($emp) > 0) {
    			$score = DB::SELECT("SELECT * from oculus_results where employee_id = '".$employee_id."'");

	    		$response = array(
					'status' => true,
					'message' => 'Success',
					'score' => $score
				);
				return Response::json($response);
    		}else{
    			$response = array(
					'status' => false,
					'message' => 'Failed'
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
}
