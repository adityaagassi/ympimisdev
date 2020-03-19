<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\ActivityList;
use App\PushBlockMaster;
use App\PushBlockRecorder;
use App\PushBlockRecorderTemp;
use App\PushBlockRecorderResume;
use App\CodeGenerator;
use App\User;
use App\RcPushPullLog;
use App\RcCameraKangoLog;
use App\PlcCounter;
use App\Libraries\ActMLEasyIf;
use Response;
use DataTables;
use Excel;
use File;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Visitor;

class OfficeClockController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    public function index()
    {
    	date_default_timezone_set('Asia/Jakarta');
		$date = date('m/d/Y H:i:s a', time());
    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));
    	return view('displays.office_clock')->with('page', 'Clock')->with('head', 'Clock')->with('dateTitle',$dateTitle)->with('date',$date);
    }

    public function fetchVisitor()
    {
    	$plc = PlcCounter::where('origin_group_code','visitor')->first();
    	$plc_lobby = PlcCounter::where('origin_group_code','visitor_lobby')->first();

    	$counter = $plc->plc_counter;
    	$id_plc = $plc->id;

    	$counter_lobby = $plc_lobby->plc_counter;
    	$id_plc_lobby = $plc_lobby->id;

    	$visitor = Visitor::where('destination','Office')->get();
    	$visitor_lobby = Visitor::where('location','Lobby')->get();

    	$jumlahvisitor = count($visitor);
    	$jumlahvisitor_lobby = count($visitor_lobby);

    	if ($jumlahvisitor != $counter) {
    		$visitors = DB::SELECT("
    		SELECT
				company,
				name,
				department,
				location
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee 
			WHERE
				destination = 'Office' 
				and location = 'Security'
			ORDER BY
				visitors.id DESC 
				LIMIT 1
			");

			$plccounter = PlcCounter::find($id_plc);
			$plccounter->plc_counter = $jumlahvisitor;
			$plccounter->save();
    	}

    	if ($jumlahvisitor_lobby != $counter_lobby) {
    		$visitors_lobby = DB::SELECT("
    		SELECT
				company,
				name,
				department,
				location
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee 
			WHERE
				destination = 'Office' 
				and location = 'Lobby'
			ORDER BY
				visitors.updated_at DESC 
				LIMIT 1
			");

			$plccounter_lobby = PlcCounter::find($id_plc_lobby);
			$plccounter_lobby->plc_counter = $jumlahvisitor_lobby;
			$plccounter_lobby->save();
    	}

    	if (isset($visitors)) {
    		$response = array(
				'status' => true,
				'visitors' => $visitors,
				'visitors_lobby' => '',
			);
			return Response::json($response);
    	}else if(isset($visitors_lobby)){
    		$response = array(
				'status' => true,
				'visitors_lobby' => $visitors_lobby,
				'visitors' => '',
			);
			return Response::json($response);
    	}else{
    		$response = array(
				'status' => false
			);
			return Response::json($response);
    	}
    }

    public function index2()
    {
    	date_default_timezone_set('Asia/Jakarta');
		$date = date('m/d/Y H:i:s a', time());
    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));
    	return view('displays.office_clock2')->with('page', 'Clock')->with('head', 'Clock')->with('dateTitle',$dateTitle)->with('date',$date);
    }

    public function fetchVisitor2()
    {
    	$plc = PlcCounter::where('origin_group_code','visitor2')->first();
    	$plc_lobby = PlcCounter::where('origin_group_code','visitor_lobby2')->first();

    	$counter = $plc->plc_counter;
    	$id_plc = $plc->id;

    	$counter_lobby = $plc_lobby->plc_counter;
    	$id_plc_lobby = $plc_lobby->id;

    	$visitor = Visitor::where('destination','Office')->get();
    	$visitor_lobby = Visitor::where('location','Lobby')->get();

    	$jumlahvisitor = count($visitor);
    	$jumlahvisitor_lobby = count($visitor_lobby);

    	if ($jumlahvisitor != $counter) {
    		$visitors = DB::SELECT("
    		SELECT
				company,
				name,
				department,
				location
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee 
			WHERE
				destination = 'Office' 
				and location = 'Security'
			ORDER BY
				visitors.id DESC 
				LIMIT 1
			");

			$plccounter = PlcCounter::find($id_plc);
			$plccounter->plc_counter = $jumlahvisitor;
			$plccounter->save();
    	}

    	if ($jumlahvisitor_lobby != $counter_lobby) {
    		$visitors_lobby = DB::SELECT("
    		SELECT
				company,
				name,
				department,
				location
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee 
			WHERE
				destination = 'Office' 
				and location = 'Lobby'
			ORDER BY
				visitors.updated_at DESC 
				LIMIT 1
			");

			$plccounter_lobby = PlcCounter::find($id_plc_lobby);
			$plccounter_lobby->plc_counter = $jumlahvisitor_lobby;
			$plccounter_lobby->save();
    	}

    	if (isset($visitors)) {
    		$response = array(
				'status' => true,
				'visitors' => $visitors,
				'visitors_lobby' => '',
			);
			return Response::json($response);
    	}else if(isset($visitors_lobby)){
    		$response = array(
				'status' => true,
				'visitors_lobby' => $visitors_lobby,
				'visitors' => '',
			);
			return Response::json($response);
    	}else{
    		$response = array(
				'status' => false
			);
			return Response::json($response);
    	}
    }

    public function guest_room()
    {
    	$apiKey = "GYhzWhGEkprqemup8Ps7VVts2jrt9kU8";
		$cityId = "208971";
		$googleApiUrl = "http://dataservice.accuweather.com/forecasts/v1/daily/1day/".$cityId."?apikey=".$apiKey."&language=en-us&details=true&metric=true";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);

		curl_close($ch);
		$weather = json_decode($response);
		$currentTime = time();

    	$dateTitle = date("d F Y", strtotime(date('Y-m-d')));
    	return view('displays.guest_room')
    	->with('page', 'Clock')
    	->with('head', 'Clock')
    	->with('dateTitle',$dateTitle)
    	->with('weather',$weather)
    	->with('currentTime',$currentTime);
    }

    public function fetchWeather()
    {
    	$apiKey = "GYhzWhGEkprqemup8Ps7VVts2jrt9kU8";
		$cityId = "208971";
		$googleApiUrl = "http://dataservice.accuweather.com/forecasts/v1/daily/1day/".$cityId."?apikey=".$apiKey."&language=en-us&details=true&metric=true";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);

		curl_close($ch);
		$weather = json_decode($response);
		$currentTime = time();

		$response = array(
			'status' => true,
			'weather' => $weather,
			'currentTime' => $currentTime,
		);
		return Response::json($response);
    }

    public function guest_room2()
    {
    	$apiKey = "GYhzWhGEkprqemup8Ps7VVts2jrt9kU8";
		$cityId = "208971";
		$googleApiUrl = "http://dataservice.accuweather.com/forecasts/v1/daily/1day/208971?apikey=GYhzWhGEkprqemup8Ps7VVts2jrt9kU8&language=en-us&details=true&metric=true";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);

		curl_close($ch);
		$weather = $response;
		$currentTime = time();

    	$dateTitle = date("d F Y", strtotime(date('Y-m-d')));
    	return view('displays.guest_room2')
    	->with('page', 'Clock')
    	->with('head', 'Clock')
    	->with('dateTitle',$dateTitle)
    	->with('weather',$weather)
    	->with('currentTime',$currentTime);
    }
}
