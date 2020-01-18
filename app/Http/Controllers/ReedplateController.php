<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;

class ReedplateController extends Controller
{
    public function index()
    {
    	$user = DB::table('reedplates')
        ->join('employees', 'reedplates.employee_id', '=', 'employees.employee_id')
        ->select('reedplates.*', 'employees.name',db::raw('acronym(name) as kode'))
        ->get();

        return view('beacons.reedplate.reedplateMap', array(
          'title' => 'Smart Tracking Operator Reedplate',
          'title_jp' => 'リードプレート作業者の位置把握スマートシステム',
          'user' => $user
      ))->with('page', 'reedplate');
    }

    public function getUser()
    {
    	$getUser = DB::table('reedplates')
    	->join('employees', 'reedplates.employee_id', '=', 'employees.employee_id')
    	->select('reedplates.*', 'employees.name',db::raw('acronym(name) as kode'))
    	->get();

    	$response = array(
    		'status' => true,
    		'data' => $getUser,
    	);
    	return Response::json($response);
    }

    public function fetch_log(Request $request)
    {

        $date = '';
        if(strlen($request->get("tanggal")) > 0){
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        }else{
            $date = date('Y-m-d');
        }

        $dateTitle = date("d M Y", strtotime($date));

    	$fetch_data = db::select('
    	select mstr.`name`, mstr.major, mstr.minor, mstr.reader, mstr.lokasi ,IFNULL(datas.jam_kerja,0) jam_kerja, acronym(mstr.`name`) as kode from
            (SELECT major, minor, `name`, SUM(jam_kerja) jam_kerja, reader FROM
            (SELECT employees.`name`, reedplate_logs.major, reedplate_logs.minor,reedplate_logs.reader, ROUND(SUM(TIME_TO_SEC(timediff(reedplate_logs.selesai, reedplate_logs.mulai)) /60),0) as jam_kerja from reedplate_logs JOIN reedplates on reedplates.minor = reedplate_logs.minor JOIN employees on employees.employee_id = reedplates.employee_id where date(reedplate_logs.mulai) = "'.$date.'" GROUP BY reedplate_logs.minor, reedplate_logs.major, employees.`name`, reedplate_logs.reader
                        
            UNION
                        
            SELECT employees.`name`, reedplate_temps.major, reedplate_temps.minor,reedplate_temps.reader, ROUND(TIME_TO_SEC(TIMEDIFF(NOW(),reedplate_temps.mulai))/60,0) as jam_kerja FROM reedplate_temps JOIN reedplates on reedplates.minor = reedplate_temps.minor JOIN employees on employees.employee_id = reedplates.employee_id WHERE DATE(reedplate_temps.mulai) = "'.$date.'" GROUP BY reedplate_temps.mulai, reedplate_temps.minor, reedplate_temps.major, employees.`name`, reedplate_temps.reader) AS gabung
            GROUP BY major, minor, `name`, reader) as datas
            
            RIGHT JOIN 
                
            (SELECT reedplate_distances.lokasi,reedplates.employee_id, `name`,major,minor, reedplate_distances.reader from reedplates cross join reedplate_distances LEFT JOIN employees on reedplates.employee_id = employees.employee_id) as mstr
            on datas.major = mstr.major and datas.minor = mstr.minor and datas.reader = mstr.reader ORDER BY reader ASC, minor ASC
        ');

        $fetch_machine = db::select('
            select reedplate_logs.reader, ROUND(SUM(TIME_TO_SEC(timediff(reedplate_logs.selesai, reedplate_logs.mulai)) /60),0) as jam_kerja, reedplate_distances.lokasi from reedplate_logs JOIN reedplate_distances on reedplate_distances.reader = reedplate_logs.reader where date(reedplate_logs.mulai) = "'.$date.'" GROUP BY reedplate_logs.reader,reedplate_distances.lokasi

            UNION

            select reedplate_temps.reader, ROUND(TIME_TO_SEC(TIMEDIFF(NOW(),reedplate_temps.mulai))/60,0) as jam_kerja, reedplate_distances.lokasi FROM reedplate_temps JOIN reedplate_distances on reedplate_distances.reader = reedplate_temps.reader WHERE DATE(reedplate_temps.mulai) = "'.$date.'" GROUP BY reedplate_temps.mulai, reedplate_temps.reader,reedplate_distances.lokasi');

    	$response = array(
    		'status' =>true,
    		'data' => $fetch_data,
            'data_mesin' => $fetch_machine,
            'date' => $dateTitle
    	);
    	return Response::json($response);
    }

  

}


