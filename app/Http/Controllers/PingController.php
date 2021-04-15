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
use App\PingSpeedtest;
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

    // $ping = new \JJG\Ping($ip);
    // $latency = $ping->ping();
    // if ($latency !== false) {
    //   print 'Latency is ' . $latency . ' ms';
    // }
    // else {
    //   print 'Host could not be reached.';
    // }

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
      $title_jp = 'pingサーバーのステータス';

      return view('rooms.serverPing', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room');    
    } 

    else if($id == 'database'){
      $title = 'Database Server Status';
      $title_jp = 'データベースサーバーのステータス';

      return view('rooms.serverDatabase', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room Database');    
    }   

    else if($id == 'mirai_status'){
      $title = 'MIRAI Server Status';
      $title_jp = 'MIRAIサーバーのステータス';

      return view('rooms.serverNetworkUsage', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room Network Usage');    
    }

    else if($id == 'app_status'){
      $title = 'System Server Status';
      $title_jp = 'システムサーバーのステータス';

      return view('rooms.serverAppStatus', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room All App Status');    
    }

    else if($id == 'speedtest'){
      $title = 'Speedtest Information';
      $title_jp = 'スピードテスト情報';

      return view('rooms.serverSpeedtest', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Server Room SpeedTest');    
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
      'uptime' => $dtF->diff($dtT)->format('%aDay %hHour %iMin'),
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



  public function AllHardiskPingStatus()
  {
    $result = "";
    $api = 'http://10.109.52.2/phpsysinfo/xml.php?json';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_URL, $api);
    $result=curl_exec($ch);
    curl_close($ch);

    $arr = json_decode($result, true);

    $hardisk_free_mirai_db = $arr['FileSystem']['Mount'][0]['@attributes']['Free'] / 1073741824;
    $hardisk_used_mirai_db = $arr['FileSystem']['Mount'][0]['@attributes']['Used'] / 1073741824;

    $result2 = "";
    $api2 = 'http://10.109.52.1:887/phpsysinfo/xml.php?json';
    
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch2, CURLOPT_URL, $api2);
    $result2=curl_exec($ch2);
    curl_close($ch2);

    $arr2 = json_decode($result2, true);

    $hardisk_free_ympiserver = $arr2['FileSystem']['Mount'][1]['@attributes']['Free'] / 1099511627776;
    $hardisk_used_ympiserver = $arr2['FileSystem']['Mount'][1]['@attributes']['Used'] / 1099511627776;

    $result3 = "";
    $api3 = 'http://10.109.52.9:8080/phpsysinfo/xml.php?json';
    
    $ch3 = curl_init();
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch3, CURLOPT_URL, $api3);
    $result3=curl_exec($ch3);
    curl_close($ch3);

    $arr3 = json_decode($result3, true);
    
    $hardisk_free_sunfish_db = $arr3['FileSystem']['Mount'][1]['@attributes']['Free'] / 1073741824;
    $hardisk_used_sunfish_db = $arr3['FileSystem']['Mount'][1]['@attributes']['Used'] / 1073741824;

    $result4 = "";
    $api4 = 'http://10.109.48.3/phpsysinfo/xml.php?json';
    
    $ch4 = curl_init();
    curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch4, CURLOPT_URL, $api4);
    $result4=curl_exec($ch4);
    curl_close($ch4);

    $arr4 = json_decode($result4, true);

    $hardisk_free_reportman = $arr4['FileSystem']['Mount'][4]['@attributes']['Free'] / 1073741824;
    $hardisk_used_reportman = $arr4['FileSystem']['Mount'][4]['@attributes']['Used'] / 1073741824;

    $data_ping = Pinglog::whereRaw('DATE_FORMAT(created_at,"%Y-%m-%d %H:%i:%s") >= "'.date('Y-m-d 06:00:00').'"')
    // ->where('remark','=','mirai db')
    ->select('*', db::raw('DATE_FORMAT(created_at, "%H:%i") as data_time'))
    ->orderBy('id', 'asc')
    ->get();

    $response = array(
      'status' => true,
      'message' => 'Data Berhasil Ditemukan',
      'hardisk_free_mirai_db' => $hardisk_free_mirai_db,
      'hardisk_used_mirai_db' => $hardisk_used_mirai_db,
      'hardisk_free_ympiserver' => $hardisk_free_ympiserver,
      'hardisk_used_ympiserver' => $hardisk_used_ympiserver,
      'hardisk_free_sunfish_db' => $hardisk_free_sunfish_db,
      'hardisk_used_sunfish_db' => $hardisk_used_sunfish_db,
      'hardisk_free_reportman' => $hardisk_free_reportman,
      'hardisk_used_reportman' => $hardisk_used_reportman,
      'data_ping' => $data_ping
    );

    return Response::json($response);
  }

  public function ServerRoomSpeedtest()
  {
    $path = "speedtest/speedtest.txt";
    $data = json_decode(file_get_contents($path), true);

    $download = $data['download']/1000000;
    $upload = $data['upload']/1000000;

    $ping = PingSpeedtest::create([
      'download' => number_format($download, 2, '.', ''),
      'upload' => number_format($upload, 2, '.', ''),
      'ping' => number_format($data['ping'], 2, '.', ''),
      'city' => $data['server']['name'],
      'country' => $data['server']['country'],
      'address' => $data['client']['ip'],
      'service_provider' => $data['client']['isp'],
      'created_by' => Auth::user()->username
   ]);

    $ping->save();

    $speedtest = PingSpeedtest::whereRaw('DATE_FORMAT(created_at,"%Y-%m-%d %H:%i:%s") >= "'.date('Y-m-d 06:00:00').'"')
    ->select('*', db::raw('DATE_FORMAT(created_at, "%H:%i") as data_time'))
    ->orderBy('id', 'asc')
    ->get();

    $response = array(
      'status' => true,
      'message' => 'Data Berhasil Didapatkan',
      'speedtest' => $speedtest,
    );
    return Response::json($response);
  }
}