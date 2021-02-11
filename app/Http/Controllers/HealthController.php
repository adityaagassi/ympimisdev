<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\User;
use App\HealthIndicator;
use Response;
use DataTables;
use Carbon\Carbon;

class HealthController extends Controller
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

  	public function index($loc)
  	{
  		if ($loc == 'gme') {
  			$title = 'GM - Ekspatriat';
  			$title_jp = '部長 - 駐在員 健康状況指標';
  		}
  		return view('health.index')
  		->with('page', 'Health Indicator')
  		->with('loc', $loc)
  		->with('title', $title)
  		->with('title_jp', $title_jp);
  	}

  	public function fetchHealth(Request $request)
  	{
  		try {
  			$id_user = Auth::id();

  			   $date_from = $request->get('date_from');
	           $date_to = $request->get('date_to');
	           $now = date('Y-m-d');

	           if ($date_from == '') {
	                if ($date_to == '') {
	                     $whereDate = 'AND DATE(time_at) BETWEEN CONCAT(DATE_FORMAT("'.$now.'" - INTERVAL 7 DAY,"%Y-%m-%d")) AND "'.$now.'"';
	                }else{
	                     $whereDate = 'AND DATE(time_at) BETWEEN CONCAT(DATE_FORMAT("'.$date_to.'" - INTERVAL 7 DAY,"%Y-%m-%d")) AND "'.$date_to.'"';
	                }
	           }else{
	                if ($date_to == '') {
	                     $whereDate = 'AND DATE(time_at) BETWEEN "'.$date_from.'" AND DATE(NOW())';
	                }else{
	                     $whereDate = 'AND DATE(time_at) BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
	                }
	           }

	           $whereType = "";
	           if ($request->get('type') == "") {
	           	$whereType = "";
	           }else{
	           	$whereType = "AND type = '".$request->get('type')."'";
	           }

  			$health = DB::SELECT("SELECT
					* 
				FROM
					`health_indicators`
					JOIN users ON users.id = health_indicators.created_by 
				WHERE
					health_indicators.created_by = ".$id_user."
					".$whereDate." ".$whereType."
				ORDER BY
					time_at DESC");

  			$response = array(
	            'status' => true,
	            'health' => $health,
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

  	public function uploadHealth(Request $request)
  	{
  		try{
	           $id_user = Auth::id();

	              $file = $request->file('file');
	              $file_name = 'temp_'. MD5(date("YmdHisa")) .'.'.$file->getClientOriginalExtension();
	              $file->move('data_file/xml/', $file_name);

	              $xml = 'data_file/xml/' . $file_name;
	              $xmlparse = simplexml_load_file($xml) or die("Error: Cannot create object");
	              for ($i=0; $i < count($xmlparse->entry); $i++) { 
	              	for ($j=0; $j < count($xmlparse->entry[$i]->organizer->component); $j++) { 
	              		if ($xmlparse->entry[$i]->organizer->component[$j]->observation->text->type == 'HKQuantityTypeIdentifierHeartRate') {
	              			$source_name = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->sourceName;
	              			$unit = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->unit;
	              			$value = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->value;
	              			$type = 'Heart Rate';
	              			$type_id = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->type;
	              			$remark = $request->get('loc');
	              			// var_dump($xmlparse->entry[$i]->organizer->component[$j]->observation->effectiveTime->low->attributes());
	              			$attr = $xmlparse->entry[$i]->organizer->component[$j]->observation->effectiveTime->low->attributes();

	              			for ($l=0; $l < count($attr); $l++) { 
	              				$attrs = $attr[$l][0];
	              			}

	              			$attrs2 = explode('+', $attrs);
	              			$year = substr($attrs2[0], 0, 4);
	              			$month = substr($attrs2[0], 4, 2);
	              			$day = substr($attrs2[0], 6, 2);

	              			$hour = substr($attrs2[0], 8, 2);
	              			$minute = substr($attrs2[0], 10, 2);
	              			$second = substr($attrs2[0], 12, 2);
	              			$at = date('Y-m-d H:i:s',strtotime($year."-".$month."-".$day." ".$hour.":".$minute.":".$second. ' + 7 hours'));

	              			$healthcreate = HealthIndicator::create([
	              				'type' => $type,
	              				'type_id' => $type_id,
	              				'source_name' => $source_name,
	              				'value' => $value,
	              				'unit' => $unit,
	              				'remark' => $remark,
	              				'time_at' => $at,
	              				'created_by' => Auth::id()
	              			]);
	              		}else if ($xmlparse->entry[$i]->organizer->component[$j]->observation->text->type == 'HKQuantityTypeIdentifierOxygenSaturation') {
	              			$source_name = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->sourceName;
	              			$unit = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->unit;
	              			$value = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->value;
	              			$type = 'Oxygen Rate';
	              			$type_id = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->type;
	              			$remark = $request->get('loc');

	              			$attr = $xmlparse->entry[$i]->organizer->component[$j]->observation->effectiveTime->low->attributes();

	              			for ($l=0; $l < count($attr); $l++) { 
	              				$attrs = $attr[$l][0];
	              			}

	              			$attrs2 = explode('+', $attrs);
	              			$year = substr($attrs2[0], 0, 4);
	              			$month = substr($attrs2[0], 4, 2);
	              			$day = substr($attrs2[0], 6, 2);

	              			$hour = substr($attrs2[0], 8, 2);
	              			$minute = substr($attrs2[0], 10, 2);
	              			$second = substr($attrs2[0], 12, 2);
	              			$at = date('Y-m-d H:i:s',strtotime($year."-".$month."-".$day." ".$hour.":".$minute.":".$second. ' + 7 hours'));

	              			$healthcreate = HealthIndicator::create([
	              				'type' => $type,
	              				'type_id' => $type_id,
	              				'source_name' => $source_name,
	              				'value' => $value,
	              				'unit' => $unit,
	              				'remark' => $remark,
	              				'time_at' => $at,
	              				'created_by' => Auth::id()
	              			]);
	              		}else if ($xmlparse->entry[$i]->organizer->component[$j]->observation->text->type == 'HKQuantityTypeIdentifierBodyMass') {
	              			$source_name = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->sourceName;
	              			$unit = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->unit;
	              			$value = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->value;
	              			$type = 'Body Mass';
	              			$type_id = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->type;
	              			$remark = $request->get('loc');

	              			$attr = $xmlparse->entry[$i]->organizer->component[$j]->observation->effectiveTime->low->attributes();

	              			for ($l=0; $l < count($attr); $l++) { 
	              				$attrs = $attr[$l][0];
	              			}

	              			$attrs2 = explode('+', $attrs);
	              			$year = substr($attrs2[0], 0, 4);
	              			$month = substr($attrs2[0], 4, 2);
	              			$day = substr($attrs2[0], 6, 2);

	              			$hour = substr($attrs2[0], 8, 2);
	              			$minute = substr($attrs2[0], 10, 2);
	              			$second = substr($attrs2[0], 12, 2);
	              			$at = date('Y-m-d H:i:s',strtotime($year."-".$month."-".$day." ".$hour.":".$minute.":".$second. ' + 7 hours'));

	              			$healthcreate = HealthIndicator::create([
	              				'type' => $type,
	              				'type_id' => $type_id,
	              				'source_name' => $source_name,
	              				'value' => $value,
	              				'unit' => $unit,
	              				'remark' => $remark,
	              				'time_at' => $at,
	              				'created_by' => Auth::id()
	              			]);
	              		}else if ($xmlparse->entry[$i]->organizer->component[$j]->observation->text->type == 'HKQuantityTypeIdentifierHeight') {
	              			$source_name = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->sourceName;
	              			$unit = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->unit;
	              			$value = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->value;
	              			$type = 'Body Height';
	              			$type_id = $xmlparse->entry[$i]->organizer->component[$j]->observation->text->type;
	              			$remark = $request->get('loc');

	              			$attr = $xmlparse->entry[$i]->organizer->component[$j]->observation->effectiveTime->low->attributes();

	              			for ($l=0; $l < count($attr); $l++) { 
	              				$attrs = $attr[$l][0];
	              			}

	              			$attrs2 = explode('+', $attrs);
	              			$year = substr($attrs2[0], 0, 4);
	              			$month = substr($attrs2[0], 4, 2);
	              			$day = substr($attrs2[0], 6, 2);

	              			$hour = substr($attrs2[0], 8, 2);
	              			$minute = substr($attrs2[0], 10, 2);
	              			$second = substr($attrs2[0], 12, 2);
	              			$at = date('Y-m-d H:i:s',strtotime($year."-".$month."-".$day." ".$hour.":".$minute.":".$second. ' + 7 hours'));

	              			$healthcreate = HealthIndicator::create([
	              				'type' => $type,
	              				'type_id' => $type_id,
	              				'source_name' => $source_name,
	              				'value' => $value,
	              				'unit' => $unit,
	              				'remark' => $remark,
	              				'time_at' => $at,
	              				'created_by' => Auth::id()
	              			]);
	              		}
	              	}
	              }

	           $response = array(
	            'status' => true,
	            'message' => 'Upload file success',
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
}
