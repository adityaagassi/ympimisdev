<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\IpList;
use App\PingLog;
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


}
