<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\User;
use Response;
use DataTables;

class TemperatureController extends Controller
{


	public function suhu_1()
	{
		return view('beacons.temperatur.suhu1', array(
			'title' => 'Monitoring Sensor Suhu R.SERVER',
			'title_jp' => 'サーバールーム温度センサーの監視'))->with('page', 'suhu');
	}
 //    public function suhu_1()
	// {	
	// 	//lokasi letak view
	// 	return view('beacons.temperatur.suhu1');
	// }


	public function index_maps()
	{
		return view('beacons.temperatur.map', array(
			'title' => 'Temperature Map',
			'title_jp' => '温度分布'))->with('page', 'Map');
	}
	// public function index_maps()
	// {	
	// 	//lokasi letak view
	// 	return view('beacons.temperatur.map');
	// }

public function suhu_2()
	{
		return view('beacons.temperatur.suhu2', array(
			'title' => 'Monitoring Sensor Suhu Office',
			'title_jp' => '事務所温度センサーの監視'))->with('page', 'suhu1');
	}

	// public function suhu_2()
	// {	
	// 	//lokasi letak view
	// 	return view('beacons.temperatur.suhu2');
	// }

    public function data_suhu_1(Request $request)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select *,DATE_FORMAT(suhus.datetime,'%H:%i:%s') as time from suhus where DATE_FORMAT(suhus.datetime,'%Y-%m-%d') = CURDATE() ORDER BY id DESC LIMIT 10");
      $monthTitle = date("F Y", strtotime($bulan));

     

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
     
      );

      return Response::json($response);
    }

    public function log_map_suhu1(Request $request) //server
    {
      
      $data = DB::select("SELECT temperature FROM suhus ORDER BY id DESC LIMIT 1");
      


      $response = array(
        'status' => true,
        'datas' => $data,


      );

      return Response::json($response);
    }

    public function log_map_suhu2(Request $request) //office
    {
      
      $data = DB::select("SELECT temperature FROM suhuss ORDER BY id DESC LIMIT 1");
      


      $response = array(
        'status' => true,
        'datas' => $data,


      );

      return Response::json($response);
    }



    public function data_suhu_2(Request $request)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select *,DATE_FORMAT(suhuss.datetime,'%H:%i:%s') as time from suhuss where DATE_FORMAT(suhuss.datetime,'%Y-%m-%d') = CURDATE() ORDER BY id DESC LIMIT 10");
      $monthTitle = date("F Y", strtotime($bulan));

     

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
     
      );

      return Response::json($response);
    }


}
