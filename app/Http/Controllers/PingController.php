<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\IpList;
use App\PingLog;
use App\PingNetworkUsageLog;
use Response;
use DataTables;

class PingController extends Controller
{
    public function __construct()
    {
     $this->middleware('auth');
   }

   public function indexIpMonitoring(){

          // $ip = "172.17.128.18";
      		// exec("ping -n 1 $ip", $output, $status);
      		// print_r($output);
      		// exit;

    $ips = IpList::whereNull('deleted_at')->get();

    $location = IpList::whereNull('deleted_at')->select('location')->distinct()->get();

    $title = 'Internet Protocol Monitoring';
    $title_jp = 'IP管理';

    return view('ping.ip_monitoring', array(
     'title' => $title,
     'title_jp' => $title_jp,
     'ip' => $ips,
     'location' => $location
   ))->with('page', $title);
  }


  public function fetch(Request $request)
  {
    try{
      // $detail = IpList::get();

      // $detail = "Select * from ip_lists left join ping_logs pg on ip_lists.ip = pg.ip";
      // $getlastip = DB::select($detail);

      $addlocation = "";
      if($request->get('location') != null) {
        $locations = explode(",", $request->get('location'));
        $location = "";

        for($x = 0; $x < count($locations); $x++) {
          $location = $location."'".$locations[$x]."'";
          if($x != count($locations)-1){
            $location = $location.",";
          }
        }
        $addlocation = "and location in (".$location.") ";
      }


      $detail = "select * from ip_lists where deleted_at is null ".$addlocation."";
      $getlastip = DB::select($detail);

      if($getlastip == null)
      {
        $getlastip = IpList::get();
      }


      $location = "";
      if($request->get('location') != null) {
        $locations = explode(",", $request->get('location'));
        for($x = 0; $x < count($locations); $x++) {
          $location = $location." ".$locations[$x]." ";
          if($x != count($locations)-1){
            $location = $location."&";
          }
        }
      }else{
        $location = "";
      }
      $location = strtoupper($location);


      $response = array(
        'status' => true,
        'data' => $getlastip,
        'title' => $location
      );
      return Response::json($response);

    }
    catch (QueryException $beacon){
      $error_code = $beacon->errorInfo[1];
      if($error_code == 1062){
       $response = array(
        'status' => false,
        'datas' => "Name already exist",
      );
       return Response::json($response);
     }
     else{
       $response = array(
        'status' => false,
        'datas' => "Update Error.",
      );
       return Response::json($response);
     }
   }
  }

  public function fetch_hit($ip)
  {
    $data = exec("ping -n 1 $ip", $output, $status);

    $response = array(
      'status' => true,
      'data' => $data,
      'output' => $output,
      'sta' => $status
    );
    return Response::json($response);
      // return Response::json($data);
  }

  public function ip_log(Request $request)
  {
    try{
      $id_user = Auth::id();
                // $interview_id = $request->get('interview_id');
                // $time = $request->get('hasil_hit');
                // $hasil_time = substr($time,42);
      PingLog::create([
        'ip' => $request->get('ip'),
        'remark' => $request->get('remark'),
        'time' => $request->get('hasil_hit'),
        'status' => $request->get('status'),
        'created_by' => $id_user
      ]);

      $response = array(
        'status' => true,
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

  public function ServerRoom(){
    return view('rooms.serverRoom')->with('page', 'Server Room');
  }

  public function ServerRoomPing($id)
  {
    if($id == 'ping'){
      $title = 'Ping Server Status';
      $title_jp = '';

      return view('rooms.serverPing', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room');    
    } 

    else if($id == 'database'){
      $title = 'Database Server Status';
      $title_jp = '';

      return view('rooms.serverDatabase', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room Database');    
    }   

    else if($id == 'mirai_status'){
      $title = 'MIRAI Server Status';
      $title_jp = '';

      return view('rooms.serverNetworkUsage', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room Network Usage');    
    }

    else if($id == 'app_status'){
      $title = 'All App Server Status';
      $title_jp = '';

      return view('rooms.serverAppStatus', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room All App Status');    
    }  
  }

  public function ServerRoomPingTrend()
  {

    $data_ping = Pinglog::whereRaw('DATE_FORMAT(created_at,"%Y-%m-%d %H:%i:%s") >= "'.date('Y-m-d 06:00:00').'"')
    ->where('remark','=','Internet')
    ->select('*', db::raw('DATE_FORMAT(created_at, "%H:%i") as data_time'))
    ->orderBy('id', 'asc')
    ->get();

    $data_vpn = Pinglog::whereRaw('DATE_FORMAT(created_at,"%Y-%m-%d %H:%i:%s") >= "'.date('Y-m-d 06:00:00').'"')
    ->where('remark','=','vpn')
    ->select('*', db::raw('DATE_FORMAT(created_at, "%H:%i") as data_time'))
    ->orderBy('id', 'asc')
    ->get();

    $response = array(
      'status' => true,
      'message' => 'Data Berhasil Didapatkan',
      'data_ping' => $data_ping,
      'data_vpn' => $data_vpn
    );
    return Response::json($response);
  }

  public function PostNetworkUsage()
  {
    $result = "";
    $api = 'http://10.109.52.4/phpsysinfo/xml.php?json';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_URL, $api);
    $result=curl_exec($ch);
    curl_close($ch);

    $arr = json_decode($result, true);

    $received = $arr['Network']['NetDevice'][5]['@attributes']['RxBytes'] / 1073741824;
    $sent = $arr['Network']['NetDevice'][5]['@attributes']['TxBytes'] / 1073741824;

    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@".$arr['Vitals']['@attributes']['Uptime']."");

    $datetime = date("Y-m-d H:i:s");
    $timestamp = strtotime($datetime);
    $time = $timestamp - (int)$arr['Vitals']['@attributes']['Uptime'];
    $datetime = date("Y-m-d H:i:s", $time);

    PingNetworkUsageLog::create([
      'hostname' => $arr['Vitals']['@attributes']['Hostname'],
      'ip' => $arr['Vitals']['@attributes']['IPAddr'],
      'remark' => $arr['Network']['NetDevice'][5]['@attributes']['Name'],
      'uptime' => $dtF->diff($dtT)->format('%a days, %h hours, %i minutes'),
      'last_boot' => $datetime,
      'received' => number_format($received, 2, '.', ''),
      'sent' => number_format($sent, 2, '.', ''),
      'err' => $arr['Network']['NetDevice'][5]['@attributes']['Err'],
      'drop' =>  $arr['Network']['NetDevice'][5]['@attributes']['Drops'],
      'created_by' => Auth::user()->username
    ]);


    $memory_used = $arr['Memory']['@attributes']['Used'] / 1073741824;
    $memory_free = $arr['Memory']['@attributes']['Free'] / 1073741824;

    $hardisk_free = $arr['FileSystem']['Mount'][0]['@attributes']['Free'] / 1073741824;
    $hardisk_used = $arr['FileSystem']['Mount'][0]['@attributes']['Used'] / 1073741824;

    $last_data = db::select('SELECT * FROM ping_network_usage_logs order by id desc LIMIT 1');

    $response = array(
      'status' => true,
      'message' => 'Data Berhasil Dimasukkan',
      'last_data' => $last_data,
      'memory_used' => $memory_used,
      'memory_free' => $memory_free,
      'hardisk_free' => $hardisk_free,
      'hardisk_used' => $hardisk_used
    );
    return Response::json($response);
  }


}
