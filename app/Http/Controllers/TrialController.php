<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\TagMaterial;
use App\MiddleInventory;
use App\BarrelQueue;
use File;
use App\Barrel;
use App\BarrelQueueInactive;
use App\BarrelLog;
use App\BarrelMachine;
use App\BarrelMachineLog;
use App\CodeGenerator;
use App\MiddleNgLog;
use App\MiddleLog;
use App\ErrorLog;
use App\Material;
use App\Employee;
use App\Mail\SendEmail;
use App\RfidBuffingInventory;
use Illuminate\Support\Facades\Mail;
use App\KnockDown;
use App\KnockDownDetail;
use App\TransactionTransfer;
use App\EmployeeSync;
use App\Libraries\ActMLEasyIf;
use Excel;

class TrialController extends Controller{
	var $APIurl = 'https://eu45.chat-api.com/instance150276/';
	var $token = 'owl5cvgsqlil60xf';

	public function printSummary(){
		$printer_name = 'MIS2';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$id = 'ST_4';
		$store = 'BFF3B';
		$substore = 'GROUP D_1';
		$category = '(ASSY)';
		$material_number = 'ZQ88390';
		$sloc = 'SX51';
		$description = 'A32 D-4 ASSY TUMBLING & BUFFING(YMPI)';
		$uom = 'PC';

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->text("   Monthly Stocktaking October 2020   \n");
		$printer->setTextSize(2, 2);
		$printer->text("  Summary of Counting  "."\n");
		$printer->initialize();
		$printer->setTextSize(2, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($store."\n");
		$printer->text($substore."\n");

		$printer->qrCode($id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($id."\n");

		$printer->initialize();
		$printer->setTextSize(3, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($category."\n");

		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(3, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material_number. " (". $sloc. ")\n\n");

		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->text($description."\n");
		$printer->text("Uom: ".$uom."\n");

		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);

		$printer->setReverseColors(true);
		$printer->textRaw("          HITUNG        "."|"."         REVISI        ");
		$printer->setReverseColors(false);
		$printer->textRaw(str_repeat(" ", 12)."x".str_repeat(" ", 11)." ".str_repeat(" ", 11)."x".str_repeat(" ", 12));
		$printer->textRaw("\xc0".str_repeat("\xc4", 21)."\xd9 \xc0".str_repeat("\xc4", 21)."\xd9\n");
		$printer->textRaw(str_repeat(" ", 12)."x".str_repeat(" ", 11)." ".str_repeat(" ", 11)."x".str_repeat(" ", 12));
		$printer->textRaw("\xc0".str_repeat("\xc4", 21)."\xd9 \xc0".str_repeat("\xc4", 21)."\xd9\n");
		$printer->textRaw(str_repeat(" ", 12)."x".str_repeat(" ", 11)." ".str_repeat(" ", 11)."x".str_repeat(" ", 12));
		$printer->textRaw("\xc0".str_repeat("\xc4", 21)."\xd9 \xc0".str_repeat("\xc4", 21)."\xd9\n");
		$printer->textRaw(str_repeat(" ", 12)."x".str_repeat(" ", 11)." ".str_repeat(" ", 11)."x".str_repeat(" ", 12));
		$printer->textRaw("\xc0".str_repeat("\xc4", 21)."\xd9 \xc0".str_repeat("\xc4", 21)."\xd9\n");
		$printer->textRaw(str_repeat(" ", 12)."x".str_repeat(" ", 11)." ".str_repeat(" ", 11)."x".str_repeat(" ", 12));
		$printer->textRaw("\xc0".str_repeat("\xc4", 21)."\xd9 \xc0".str_repeat("\xc4", 21)."\xd9\n");


		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_RIGHT);
		$printer->text("Print at " . Carbon::now());
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}


	public function test1(){
		for ($i=0; $i <= 5; $i++) { 
			echo $i++;
		}	
	}

	public function test2(){

		$string = "Hello world. It's a beautiful day.";
		$newString = explode(" ", $string);

		echo $newString[0].' '.$newString[4];
		
	}

	public function test3(){
		
		for ($i=0; $i < 10; $i++) { 
			for ($j=0; $j < 10; $j++) { 
				if($i <  $j){
					echo $j;
				}
			}
			echo "<br>";
		}	

	}

	public function test4(){

		$myArr = ['A','B','C','D','E','F','G','H','I','J'];

		for ($i=0; $i < 10; $i++) { 
			if(($i % 2) == 0){
				echo $myArr[$i];
			}
		}



	}

	public function trialLoc(){

		$log = db::connection('mirai_mobile')->select("select distinct latitude, longitude from quiz_logs
			where city = ''
			or province = '';");


		for($i=0; $i < count($log); $i++) {

			$loc = $this->getLocation($log[$i]->latitude, $log[$i]->longitude);
			$loc1 = json_encode($loc);
			$loc2 = explode('\"',$loc1);

			$keyVillage = array_search('village', $loc2);
			$keyResidential = array_search('residential', $loc2);
			$keyHamlet = array_search('hamlet', $loc2);
			$keyNeighbourhood = array_search('neighbourhood', $loc2);

			$keyStateDistrict = array_search('state_district', $loc2);
			$keyCity = array_search('city', $loc2);
			$keyCounty = array_search('county', $loc2);

			$keyState = array_search('state', $loc2);
			$keyPostcode = array_search('postcode', $loc2);
			$keyCountry = array_search('country', $loc2);


			if ($keyVillage && $loc2[$keyVillage+2] != ":") {
				$village = $loc2[$keyVillage+2];
			}else if($keyResidential && $loc2[$keyResidential+2] != ":") {
				$village = $loc2[$keyResidential+2];
			}else if($keyHamlet && $loc2[$keyHamlet+2] != ":") {
				$village = $loc2[$keyHamlet+2];
			}else if($keyNeighbourhood && $loc2[$keyNeighbourhood+2] != ":") {
				$village = $loc2[$keyNeighbourhood+2];
			}else{	
				$village = "";
			}

			if ($keyStateDistrict && $loc2[$keyStateDistrict + 2] != ":") {
				$city = $loc2[$keyStateDistrict + 2];
			}else if($keyCity && $loc2[$keyCity + 2] != ":") {
				$city = $loc2[$keyCity + 2];
			}else if($keyCounty && $loc2[$keyCounty+2] != ":") {
				$city = $loc2[$keyCounty+2];
			}else{	
				$city = "";
			}

			if($keyState){
				$province = $loc2[$keyState + 2];
			}else{
				$province = "";
			}

			// $data = array(
			// 	'village' => $village,
			// 	'city' => $city,
			// 	'province' => $loc2[$keyState + 2],
			// 	'postcode' => $loc2[$keyPostcode + 2],
			// 	'country' => $loc2[$keyCountry + 2]
			// );
			// dd($data);

			$lists = db::connection('mirai_mobile')
			->table('quiz_logs')
			->where('latitude', $log[$i]->latitude)
			->where('longitude', $log[$i]->longitude)
			->update([
				'village' => $village,
				'city' => $city,
				'province' => $province
			]);

		}

	}



	public function getLocation($lat, $long){

		$url = "https://locationiq.org/v1/reverse.php?key=29e75d503929a1&lat=".$lat."&lon=".$long."&format=json";
		// $url = "https://www.google.com/maps/@".$lat.",".$long."";
		$curlHandle = curl_init();
		curl_setopt($curlHandle, CURLOPT_URL, $url);
		curl_setopt($curlHandle, CURLOPT_HEADER, 0);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
		curl_setopt($curlHandle, CURLOPT_POST, 1);
		$results = curl_exec($curlHandle);
		curl_close($curlHandle);

		$response = array(
			'status' => true,
			'data' => $results,
		);
		return Response::json($response);
	}

	public function trial2(){
		$title = 'Production Achievement';
		$title_jp = '';

		return view('trial2', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Production Achievement');
	}

	public function fetchProductionAchievment(Request $request){

		$date = db::select("select week_date, remark from weekly_calendars
			where week_date <= '2020-04-16'
			and remark <> 'H'
			order by week_date desc
			limit 5");

		$datefrom = $date[4]->week_date;
		$dateto = date('Y-m-d');
		$origin_group = '043';

		if(strlen($request->get('datefrom'))>0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		if(strlen($request->get('dateto'))>0){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}
		if(strlen($request->get('origin_group'))>0){
			$origin_group = $request->get('origin_group');
		}

		$data = db::select("SELECT
			due_date,
			origin_group_code,
			sum( target ) as target,
			sum( surface_treatment ) as surface_treatment,
			sum( welding ) as welding 
			FROM
			(
			SELECT
			assy_picking_schedules.due_date,
			materials.origin_group_code,
			CEIL(
			IF
			(
			materials.origin_group_code = '043',
			sum( assy_picking_schedules.quantity ) / 34,
			IF
			( materials.origin_group_code = '042', sum( assy_picking_schedules.quantity ) / 21, sum( assy_picking_schedules.quantity ) / 20 ) 
			) 
			) AS target,
			0 AS surface_treatment,
			0 AS welding 
			FROM
			assy_picking_schedules
			LEFT JOIN materials ON materials.material_number = assy_picking_schedules.material_number 
			WHERE
			assy_picking_schedules.due_date >= '".$datefrom."' 
			AND assy_picking_schedules.due_date <= '".$dateto."' 
			AND materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND materials.origin_group_code = '".$request->get('origin_group')."'
			GROUP BY
			assy_picking_schedules.due_date,
			materials.origin_group_code UNION ALL
			SELECT
			date( kitto.histories.created_at ) AS due_date,
			ympimis.materials.origin_group_code,
			0 AS target,
			CEIL(
			IF
			(
			ympimis.materials.origin_group_code = '043',
			sum( kitto.histories.lot ) / 34,
			IF
			(
			ympimis.materials.origin_group_code = '042',
			sum( kitto.histories.lot ) / 21,
			sum( kitto.histories.lot ) / 20 
			) 
			) 
			) AS surface_treatment,
			0 AS welding 
			FROM
			kitto.histories
			LEFT JOIN kitto.materials ON kitto.materials.id = kitto.histories.completion_material_id
			LEFT JOIN ympimis.materials ON ympimis.materials.material_number = kitto.materials.material_number 
			WHERE
			date( kitto.histories.created_at ) >= '".$datefrom."' 
			AND date( kitto.histories.created_at ) <= '".$dateto."' 
			AND kitto.histories.category LIKE 'completion%' 
			AND ympimis.materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND ympimis.materials.origin_group_code = '".$request->get('origin_group')."'
			AND kitto.histories.completion_location IN ( 'SX51', 'CL51', 'FL51' ) 
			GROUP BY
			date( kitto.histories.created_at ),
			ympimis.materials.origin_group_code UNION ALL
			SELECT
			date( kitto.histories.created_at ) AS due_date,
			ympimis.materials.origin_group_code,
			0 AS target,
			0 AS surface_treatment,
			CEIL(
			IF
			(
			ympimis.materials.origin_group_code = '043',
			sum( kitto.histories.lot ) / 34,
			IF
			(
			ympimis.materials.origin_group_code = '042',
			sum( kitto.histories.lot ) / 21,
			sum( kitto.histories.lot ) / 20 
			) 
			) 
			) AS welding 
			FROM
			kitto.histories
			LEFT JOIN kitto.materials ON kitto.materials.id = kitto.histories.completion_material_id
			LEFT JOIN ympimis.materials ON ympimis.materials.material_number = kitto.materials.material_number 
			WHERE
			date( kitto.histories.created_at ) >= '".$datefrom."' 
			AND date( kitto.histories.created_at ) <= '".$dateto."' 
			AND kitto.histories.category LIKE 'completion%' 
			AND ympimis.materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND ympimis.materials.origin_group_code = '".$request->get('origin_group')."' 
			AND kitto.histories.completion_location IN ( 'SX21', 'CL21', 'FL21' ) 
			GROUP BY
			date( kitto.histories.created_at ) ,
			ympimis.materials.origin_group_code
			) AS wst 
			GROUP BY
			due_date,
			origin_group_code");


		$data2 = db::select("SELECT
			target.due_date,
			target.origin_group_code,
			target.target,
			result.result 
			FROM
			(
			SELECT
			production_schedules.due_date,
			materials.origin_group_code,
			sum( production_schedules.quantity ) AS target 
			FROM
			production_schedules
			LEFT JOIN materials ON production_schedules.material_number = materials.material_number 
			WHERE
			production_schedules.due_date >= '".$datefrom."' 
			AND production_schedules.due_date <= '".$dateto."' 
			AND materials.origin_group_code = '".$request->get('origin_group')."' 
			GROUP BY
			production_schedules.due_date,
			materials.origin_group_code 
			) AS target
			LEFT JOIN (
			SELECT
			date( flo_details.created_at ) AS date,
			flo_details.origin_group_code,
			sum( quantity ) AS result 
			FROM
			flo_details 
			WHERE
			date( flo_details.created_at ) >= '".$datefrom."' 
			AND date( flo_details.created_at ) <= '".$dateto."' 
			AND flo_details.origin_group_code = '".$request->get('origin_group')."' 
			GROUP BY
			date,
			flo_details.origin_group_code 
		) AS result ON result.date = target.due_date");

		$response = array(
			'status' => true,
			'data' => $data,
			'data2' => $data2,
			'datefrom' => $datefrom,
			'dateto' => $dateto,
			'origin_group' => $origin_group,
		);
		return Response::json($response);		
	}

	public function stocktaking(){

		$lists = db::select("SELECT
			s.id,
			s.store,
			s.category,
			s.material_number,
			mpdl.material_description,
			m.`key`,
			m.model,
			m.surface,
			mpdl.bun,
			s.location,
			mpdl.storage_location,
			v.lot_completion,
			v.lot_transfer,
			IF
			( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot 
			FROM
			stocktaking_lists s
			LEFT JOIN materials m ON m.material_number = s.material_number
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN material_volumes v ON v.material_number = s.material_number
			ORDER BY
			s.id ASC");

		foreach ($lists as $list) {
			$this->printod($list);
		}

	}

	public function temp(){
		$plc = new ActMLEasyIf(3);
		$datas = $plc->read_data('W12', 10);
		$datas = $plc->read_data('W22', 10);

		$response = array(
			'status' => true,
			'datas' => $datas
		);
		return Response::json($response);

	}

	public function printod($list){
		$printer_name = 'TESTPRINTER';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		// $id = '136';
		// $store = 'SUBASSY-CL-2B';
		// $category = '(ASSY)';
		// $material_number = 'W528860';
		// $sloc = 'CL91';
		// $description = 'CL-250N 7 ASSY CORK&PAD PACKED(YMPI) J';
		// $key = '7';
		// $model = 'CL250';
		// $surface = 'NICKEL';
		// $uom = 'PC';
		// $lot = '';

		$id = $list->id;
		$store = $list->store;
		$category = '('.$list->category.')';
		$material_number = $list->material_number;
		$sloc = $list->location;
		$description = $list->material_description;
		$key = $list->key;
		$model = $list->model;
		$surface = $list->surface;
		$uom = $list->bun;
		$lot = $list->lot;

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text("  Summary of Counting  "."\n");
		$printer->initialize();
		$printer->setTextSize(3, 3);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($store."\n");
		if($list->category == 'ASSY'){
			$printer->setReverseColors(true);			
		}
		$printer->text($category."\n");
		$printer->feed(1);
		$printer->qrCode($id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(4, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material_number."\n");
		$printer->text($sloc."\n\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 1);
		$printer->text($description."\n");
		$printer->feed(1);
		$printer->text($model."-".$key."-".$surface."\n");
		if(strlen($lot) == 0){
			$printer->text("Lot: \xDB\xDB ".$uom."\n");
			$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
			$printer->textRaw("\xb3Lot:".str_repeat("\xDB", 18)."\xb3\n");
			$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		}
		else{
			$printer->text("Lot: ".$lot." ".$uom."\n");
			$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
			$printer->textRaw("\xb3Lot:".str_repeat(" ", 18)."\xb3\n");
			$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		}
		$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
		$printer->textRaw("\xb3Z1 :".str_repeat(" ", 18)."\xb3\n");
		$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		$printer->feed(2);
		$printer->cut();
		$printer->close();
	}

	public function indexWhatsappApi()
	{
		return view('trials.whatsapp');
	}

	public function whatsapp_api()
	{
		$list = DB::SELECT("SELECT * FROM phone_lists");
		foreach ($list as $key) {
			$url = 'https://api.chat-api.com/instance150276/messages?token=owl5cvgsqlil60xf&lastMessageNumber='.$key->phone.'&last=1&chatId='.$key->phone.'%40c.us&limit=1';
			$result = file_get_contents($url);
			$data = json_decode($result, 1);
			foreach($data['messages'] as $message){ 

			    if ($message['body'] == 'Hello, Mirai!' && $message['fromMe'] == false) {

			    	$nama = explode("_",$message['chatName']);
			    	$empid = $nama[0];

			    	$author = explode("@",$message['author']);
			    	$nomor = $author[0];

			    	$employee = EmployeeSync::where('employee_id',$empid)->first();

			    	$body = 'Hello, '.$employee->name.'!\n'.
			    	'Your Department : '.$employee->division.'-'.$employee->department.' \n'.'Your Birthdate : '.$employee->birth_date.' \n'.'Your Position : '.$employee->position.'-'.$employee->grade_code;

			    	$data2 = [
				    		'phone' => $nomor,
						    'body' => $body,
					];
					$json = json_encode($data2); 
					$url = 'https://eu45.chat-api.com/instance150276/sendMessage?token=owl5cvgsqlil60xf';
					$options = stream_context_create(['http' => [
					        'method'  => 'POST',
					        'header'  => 'Content-type: application/json',
					        'content' => $json
					    ]
					]);
					$result = file_get_contents($url, false, $options);
					if ($result) {
						$response = array(
							'status' => true,
						);
					}
			    }else{
			    	$response = array(
						'status' => false,
					);
			    }
			}
		}
		return Response::json($response);
	}

	public function chat($value='')
	{
		# code...
	}

	public function index_push_pull_trial()
	{
		return view('trials.push_pull_trial', array(
          ))->with('page', 'Trial');
	}

	public function push_pull_trial(Request $request)
	{
		try {
			$id_user = Auth::id();

             $file = $request->file('file');
             $file_name = 'temp_'. MD5(date("YmdHisa")) .'.'.$file->getClientOriginalExtension();
             $file->move('data_file/push_pull/trial/', $file_name);

             $excel = 'data_file/push_pull/trial/' . $file_name;
             $rows = Excel::load($excel, function($reader) {
                // $reader->noHeading();
                // $reader->skipRows(1);

                // $reader->each(function($row) {
                // });
           })->toObject();

             // $index = 0;
             // $index2 = 0;
             // for ($i=0; $i < count($rows); $i++) { 
             // 	if ($rows[$i][0] != 0) {
             // 		$rowfix[$i] = $rows[$i][0];
             // 	}
             // 	if ($index2 != $rows[$i][0]) {
             // 		$indexfix[] = $index++;
             // 	}
             // }


            $arr = [];
            $temp = [];
            $insert = true;

            for ($i=0; $i < count($rows); $i++) { 

            	if($rows[$i][0] != 0){
					array_push($temp, $rows[$i][0]);
					$insert = true;
            	}else{
            		if($insert){
						$insert = false;
						array_push($arr, $temp);
            			$temp = [];
            		}
            	}

            }


            for ($i=0; $i < count($arr); $i++) { 
            	for ($j=0; $j < count($arr[$i]); $j++) { 
            		DB::table('push_pull_trial_temps')->insert([
			            'check_index' => $i,
			            'value' => $arr[$i][$j],
			            'created_by' => $id_user,
			            'created_at' => date('Y-m-d H:i:s'),
			            'updated_at' => date('Y-m-d H:i:s'),
			        ]);
            	}
            }

            $fix = DB::SELECT('SELECT DISTINCT ( a.check_index ),( SELECT max( VALUE ) FROM push_pull_trial_temps WHERE check_index = a.check_index ) as value
				FROM
					push_pull_trial_temps a');
            foreach ($fix as $key) {
            	DB::table('push_pull_trials')->insert([
		            'check_index' => $key->check_index+1,
		            'value' => $key->value,
		            'created_by' => $id_user,
		            'created_at' => date('Y-m-d H:i:s'),
		            'updated_at' => date('Y-m-d H:i:s'),
		        ]);
            }

            DB::table('push_pull_trial_temps')->truncate();

             $response = array(
	           'status' => true,
	           'message' => 'Upload file success',
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

	public function fetch_push_pull_trial()
	{
		try {
			$push_pull = DB::SELECT('select * from push_pull_trials');
			
			$response = array(
		        'status' => true,
		        'push_pull' => $push_pull,
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

	public function whatsapp_api2(Request $request)
	{
		// $time2 = $request->get('time2');
		// $time = $request->get('time');
		$json = file_get_contents('https://api.chat-api.com/instance150276/messages?token=owl5cvgsqlil60xf&last=1&limit=3');
		$decoded = json_decode($json,true);

		var_dump($decoded);

        //write parsed JSON-body to the file for debugging
        // ob_start();
        // // var_dump($decoded);
        // $input = ob_get_contents();
        // ob_end_clean();
        // file_put_contents('input_requests.log',$input.PHP_EOL,FILE_APPEND);

        // if(isset($decoded['messages'])){
        // //check every new message
        // foreach($decoded['messages'] as $message){
        // //delete excess spaces and split the message on spaces. The first word in the message is a command, other words are parameters
        // $text = explode(' ',trim($message['body']));
        // // echo $text;
        // // echo $message['chatId'];
        // //current message shouldn't be send from your bot, because it calls recursion
        // if(!$message['fromMe']){
        // //check what command contains the first word and call the function
        // switch(mb_strtolower($text[0],'UTF-8')){
        // case 'hi':  {$this->welcome($message['chatId'],false); break;}
        //     case 'chatId': {$this->showchatId($message['chatId']); break;}
        //     case 'time':   {$this->time($message['chatId']); break;}
        //     case 'me':     {$this->me($message['chatId'],$message['senderName']); break;}
        //     case 'file':   {$this->file($message['chatId'],$text[1]); break;}
        //     case 'ptt':     {$this->ptt($message['chatId']); break;}
        //     case 'geo':    {$this->geo($message['chatId']); break;}
        //     case 'group':  {$this->group($message['author']); break;}
        //     default:        {$this->welcome($message['chatId'],true); break;}
        //     }}}}
    }

        //this function calls function sendRequest to send a simple message
        //@param $chatId [string] [required] - the ID of chat where we send a message
        //@param $text [string] [required] - text of the message
    public function welcome($chatId, $noWelcome = false){
        $welcomeString = ($noWelcome) ? "Incorrect command\n" : "WhatsApp Demo Bot PHP\n";
        $this->sendMessage($chatId,
        $welcomeString.
        "Commands:\n".
        "1. chatId - show ID of the current chat\n".
        "2. time - show server time\n".
        "3. me - show your nickname\n".
        "4. file [format] - get a file. Available formats: doc/gif/jpg/png/pdf/mp3/mp4\n".
        "5. ptt - get a voice message\n".
        "6. geo - get a location\n".
        "7. group - create a group with the bot"
        );
	}

	public function showchatId($chatId){
    	$this->sendMessage($chatId,'chatId: '.$chatId);
    }

    public function time($chatId){
    	$this->sendMessage($chatId,date('d.m.Y H:i:s'));
    }
    //sends your nickname. it is called when the bot gets the command "me"
    //@param $chatId [string] [required] - the ID of chat where we send a message
    //@param $name [string] [required] - the "senderName" property of the message
    public function me($chatId,$name){
    	$this->sendMessage($chatId,$name);
    }
    //sends a file. it is called when the bot gets the command "file"
    //@param $chatId [string] [required] - the ID of chat where we send a message
    //@param $format [string] [required] - file format, from the params in the message body (text[1], etc)
    public function file($chatId,$format){
	    $availableFiles = array(
	    'doc' => 'document.doc',
	    'gif' => 'gifka.gif',
	    'jpg' => 'jpgfile.jpg',
	    'png' => 'pngfile.png',
	    'pdf' => 'presentation.pdf',
	    'mp4' => 'video.mp4',
	    'mp3' => 'mp3file.mp3'
	    );

	    if(isset($availableFiles[$format])){
	    $data = array(
	    'chatId'=>$chatId,
	    'body'=>'https://domain.com/PHP/'.$availableFiles[$format],
	    'filename'=>$availableFiles[$format],
	    'caption'=>'Get your file '.$availableFiles[$format]
	    );
	    $this->sendRequest('sendFile',$data);}}

	    //sends a voice message. it is called when the bot gets the command "ptt"
	    //@param $chatId [string] [required] - the ID of chat where we send a message
	    public function ptt($chatId){
	    $data = array(
	    'audio'=>'https://domain.com/PHP/ptt.ogg',
	    'chatId'=>$chatId
	    );
	    $this->sendRequest('sendAudio',$data);
	}

    //sends a location. it is called when the bot gets the command "geo"
    //@param $chatId [string] [required] - the ID of chat where we send a message
    public function geo($chatId){
	    $data = array(
	    'lat'=>51.51916,
	    'lng'=>-0.139214,
	    'address'=>'Ваш адрес',
	    'chatId'=>$chatId
	    );
	    $this->sendRequest('sendLocation',$data);
	}

    //creates a group. it is called when the bot gets the command "group"
    //@param chatId [string] [required] - the ID of chat where we send a message
    //@param author [string] [required] - "author" property of the message
    public function group($author){
	    $phone = str_replace('@c.us','',$author);
	    $data = array(
	    'groupName'=>'Group with the bot PHP',
	    'phones'=>array($phone),
	    'messageText'=>'It is your group. Enjoy'
	    );
	    $this->sendRequest('group',$data);
	}

    public function sendMessage($chatId, $text){
	    $data = array('chatId'=>$chatId,'body'=>$text);
	    $this->sendRequest('message',$data);}

	    public function sendRequest($method,$data){
	    $url = $this->APIurl.$method.'?token='.$this->token;
	    if(is_array($data)){ $data = json_encode($data);}
	    $options = stream_context_create(['http' => [
	    'method'  => 'POST',
	    'header'  => 'Content-type: application/json',
	    'content' => $data]]);
	    $response = file_get_contents($url,false,$options);
	    file_put_contents('requests.log',$response.PHP_EOL,FILE_APPEND);
	}

	public function testmail()
	{
		$mail = ['mokhamad.khamdan.khabibi@music.yamaha.com',
                	'rio.irvansyah@music.yamaha.com'];
       $bodyHtml2 = "Test Mail";

      	Mail::raw([], function($message) use($bodyHtml2,$mail) {
          $message->from('ympimis2@gmail.com', 'PT. Yamaha Musical Products Indonesia');
          $message->to($mail);
          $message->subject('Hello');
          $message->setBody($bodyHtml2, 'text/html' );
      });
	}
}
