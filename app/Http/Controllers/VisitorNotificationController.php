<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use App\Visitor;
use App\VisitorDetail;
use App\Employee;
use App\TelephoneList;
use App\VisitorId;
use App\PlcCounter;
use DataTables;
use File;
use Storage;
use App\BodyTemperature;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class VisitorNotificationController extends Controller
{
	public function __construct()
    {
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

    public function confirm_manager($id){
		try {
		    $intime = date('H:i:s');
			$visitordetail = VisitorDetail::where('id_visitor','=', $id)		
			->withTrashed()
			->update(['remark' => 'Sudah Ditemui']);

			$visitor = Visitor::where('id','=', $id)		     
			->first();
			$visitor->remark = 'Sudah Ditemui';
			$visitor->save();

			$datavisitor = Visitor::join('employee_syncs','employee_syncs.employee_id','=','visitors.employee')->where('visitors.id',$id)->first();

			$name = $datavisitor->name;
			$department = $datavisitor->department;
			$company = $datavisitor->company;

			$message = $name.' ('.$department.') telah terkonfirmasi menemui '.$company;
			return view('visitors.visitor_confirm_manager', array(
				'head' => 'Confirm Visitor Success',
				'message' => $message,
			))->with('page', 'Visitor Confirmation');
		}
		catch(\Exception $e){
			$message = $name.' ('.$department.') telah terkonfirmasi menemui '.$company;
			return view('visitors.visitor_confirm_manager', array(
				'head' => 'Confirm Visitor Success',
				'message' => $message,
			))->with('page', 'Visitor Confirmation');
		}

	}
}
