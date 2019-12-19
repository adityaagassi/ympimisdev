<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\User;
use App\standart_temperature;
use Response;
use DataTables;

class TemperatureController extends Controller
{


	public function grafikServer()
	{
		return view('beacons.temperatur.grafik_server', array(
			'title' => 'Monitoring Sensor Suhu R.SERVER',
			'title_jp' => 'サーバールーム温度センサーの監視'))->with('page', 'suhu');
	}
 //    public function suhu_1()
	// {	
	// 	//lokasi letak view
	// 	return view('beacons.temperatur.suhu1');
	// }


	public function index_map()
	{
		return view('beacons.temperatur.map', array(
			'title' => 'Temperature Map',
			'title_jp' => '温度分布'))->with('page', 'Map');
	}
	

public function grafikOffice()
	{
		return view('beacons.temperatur.grafik_office', array(
			'title' => 'Monitoring Sensor Suhu Office',
			'title_jp' => '事務所温度センサーの監視'))->with('page', 'suhu1');
	}

	  
public function log_map_server(Request $request) //server
    {
      
      $data = DB::select("SELECT * FROM (SELECT temperature, lokasi FROM `temperature` WHERE lokasi='Server' ORDER BY id DESC LIMIT 1) tmp LEFT JOIN standart_temperatures on standart_temperatures.lokasi=tmp.lokasi");
      
      $response = array(
        'status' => true,
        'datas' => $data,
      );

      return Response::json($response);
    }



    public function log_map_office(Request $request) //office
    {
      
      $data = DB::select("SELECT * FROM (SELECT temperature, lokasi FROM `temperature` WHERE lokasi='Office' ORDER BY id DESC LIMIT 1) tmp LEFT JOIN standart_temperatures on standart_temperatures.lokasi=tmp.lokasi");
      
      $response = array(
        'status' => true,
        'datas' => $data,
      );

      return Response::json($response);
    }


	public function data_suhu_server(Request $request)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select *,DATE_FORMAT(temperature.datetime,'%H:%i:%s') as time from temperature where DATE_FORMAT(temperature.datetime,'%Y-%m-%d') = CURDATE() and lokasi='Server' ORDER BY id DESC LIMIT 10");
      $monthTitle = date("F Y", strtotime($bulan));
   
      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
     );

      return Response::json($response);
    }


    public function data_suhu_office(Request $request)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select *,DATE_FORMAT(temperature.datetime,'%H:%i:%s') as time from temperature where DATE_FORMAT(temperature.datetime,'%Y-%m-%d') = CURDATE() and lokasi='Office' ORDER BY id DESC LIMIT 10");
      $monthTitle = date("F Y", strtotime($bulan));
    
      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
     );

      return Response::json($response);
    }

// User
public function standart()
	{
		$cr = standart_temperature::select('standart_temperatures.*')->get();
		return view('beacons.temperatur.standart_temperatur', array(
			'cr' => $cr

		))->with('page', 'Temperature Limit');
	}


public function edit(Request $request)
	{
		try{
			$temperatur = standart_temperature::find($request->get("id"));
			$lokasi = $temperatur['lokasi'];
			$upper_limit = $temperatur['upper_limit'];
			$lower_limit = $temperatur['lower_limit'];	

			$response = array(
				'status' => true,
				'lokasi' => $lokasi,
				'upper_limit' => $upper_limit,
				'lower_limit' => $lower_limit
			);
			return Response::json($response);

		}
		catch (QueryException $temperatur){
			$error_code = $temperatur->errorInfo[1];
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
					'datas' => "Update  Error.",
				);
				return Response::json($response);
			}
		}
	}


	public function delete($id)
	{
		$temperature = standart_temperature::find($id);
		$temperature->delete();

		return redirect('/index/standart_temperature')
		->with('status', 'Record has been deleted.')
		->with('page', 'temperature');
	}


	public function aksi_edit(Request $request)
    {
        try{
                        db::table('standart_temperatures')->where('id', '=', $request->get('id'))->update([
				'lokasi' => $request->get('lokasi'),
				'upper_limit' => $request->get('upper_limit'),
				'lower_limit' => $request->get('lower_limit')
			]);

            $response = array(
              'status' => true
            );
            return Response::json($response);

          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Already exist",
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


}
