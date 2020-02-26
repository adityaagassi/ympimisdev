<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use App\ReedplateTemp;

class ReedplateController extends Controller
{
    public function index()
    {
    	$user = DB::table('reedplates')
        ->join('employee_syncs', 'reedplates.employee_id', '=', 'employee_syncs.employee_id')
        ->select('reedplates.*', 'employee_syncs.name',db::raw('acronym(name) as kode'))
        ->get();

        return view('beacons.reedplate.reedplateMap', array(
          'title' => 'Smart Tracking Operator Reedplate',
          'title_jp' => 'リードプレート作業者の位置把握スマートシステム',
          'user' => $user
      ))->with('page', 'reedplate');
    }

    public function reed()
    {
        $user = DB::table('reedplates')
        ->join('employee_syncs', 'reedplates.employee_id', '=', 'employee_syncs.employee_id')
        ->select('reedplates.*', 'employee_syncs.name',db::raw('acronym(name) as kode'))
        ->get();

        return view('beacons.reedplate.reedplateTime', array(
          'title' => 'Working Time Reedplate',
          'title_jp' => 'リードプレート作業時間',
          'user' => $user
      ))->with('page', 'reedplate');
    }

    public function getUser()
    {
    	$getUser = DB::table('reedplates')
    	->join('employee_syncs', 'reedplates.employee_id', '=', 'employee_syncs.employee_id')
    	->select('reedplates.*', 'employee_syncs.name',db::raw('acronym(name) as kode'))
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
            (SELECT employee_syncs.`name`, reedplate_logs.major, reedplate_logs.minor,reedplate_logs.reader, ROUND(SUM(TIME_TO_SEC(timediff(reedplate_logs.selesai, reedplate_logs.mulai)) /60),0) as jam_kerja from reedplate_logs JOIN reedplates on reedplates.minor = reedplate_logs.minor JOIN employee_syncs on employee_syncs.employee_id = reedplates.employee_id where date(reedplate_logs.mulai) = "'.$date.'" GROUP BY reedplate_logs.minor, reedplate_logs.major, employee_syncs.`name`, reedplate_logs.reader
                        
            UNION
                        
            SELECT employee_syncs.`name`, reedplate_temps.major, reedplate_temps.minor,reedplate_temps.reader, ROUND(TIME_TO_SEC(TIMEDIFF(NOW(),reedplate_temps.mulai))/60,0) as jam_kerja FROM reedplate_temps JOIN reedplates on reedplates.minor = reedplate_temps.minor JOIN employee_syncs on employee_syncs.employee_id = reedplates.employee_id WHERE DATE(reedplate_temps.mulai) = "'.$date.'" GROUP BY reedplate_temps.mulai, reedplate_temps.minor, reedplate_temps.major, employee_syncs.`name`, reedplate_temps.reader) AS gabung
            GROUP BY major, minor, `name`, reader) as datas
            
            RIGHT JOIN 
                
            (SELECT reedplate_distances.lokasi,reedplates.employee_id, `name`,major,minor, reedplate_distances.reader from reedplates cross join reedplate_distances LEFT JOIN employee_syncs on reedplates.employee_id = employee_syncs.employee_id) as mstr
            on datas.major = mstr.major and datas.minor = mstr.minor and datas.reader = mstr.reader ORDER BY reader ASC, minor ASC
        ');

        $fetch_machine = db::select('
            select reader, lokasi, sum(jam_kerja) as jam_kerja from
                (select reedplate_logs.reader, ROUND(SUM(TIME_TO_SEC(timediff(reedplate_logs.selesai, reedplate_logs.mulai)) /60),0) as jam_kerja, reedplate_distances.lokasi from reedplate_logs JOIN reedplate_distances on reedplate_distances.reader = reedplate_logs.reader where date(reedplate_logs.mulai) = "'.$date.'" GROUP BY reedplate_logs.reader,reedplate_distances.lokasi

                            UNION

                 select reedplate_temps.reader, ROUND(SUM(TIME_TO_SEC(TIMEDIFF(NOW(),reedplate_temps.mulai)))/60,0) as jam_kerja, reedplate_distances.lokasi FROM reedplate_temps JOIN reedplate_distances on reedplate_distances.reader = reedplate_temps.reader WHERE DATE(reedplate_temps.mulai) = "'.$date.'" GROUP BY reedplate_temps.reader,reedplate_distances.lokasi) as tabel
                group by reader, lokasi');

    	$response = array(
    		'status' =>true,
    		'data' => $fetch_data,
            'data_mesin' => $fetch_machine,
            'date' => $dateTitle
    	);
    	return Response::json($response);
    }

    public function inputTemp(Request $request){
        try{
            $datas = $request->get('data');

            foreach($datas as $data){
                //update selesai ketika pindah reader
                $cekreader = 'SELECT reader from reedplate_temps WHERE major = '.$data['major'].' and minor = '.$data['minor'].'';
                $cek = DB::select($cekreader);

                if($cek[0]->reader != $data['reader']){
                    $pindahreader = 'UPDATE reedplate_temps SET reedplate_temps.selesai = now() WHERE major = "'.$data['major'].'" and minor = "'.$data['minor'].'"';
                    $read = DB::select($pindahreader);
                }

                //update selesai to now ketika melebihi jarak
                $updateselesai = 'UPDATE reedplate_temps AS a INNER JOIN reedplate_distances AS b ON a.reader = b.reader SET a.selesai = now() WHERE major='.$data['major'].' and minor='.$data['minor'].' and a.reader = "'.$data['reader'].'" and a.distance > b.distance and a.mulai is not null';

                $updates = DB::select($updateselesai);

                 //insert kan ke log ketika selesai gak kosong dan mulai gak kosong
                $insertlog = 'INSERT INTO reedplate_logs (major, minor,mulai,selesai,reader,distance) SELECT major, minor, mulai, selesai, reader, distance FROM reedplate_temps WHERE minor = "'.$data['minor'].'" and major = "'.$data['major'].'" and selesai is not null and mulai is not null';

                $insertl = DB::select($insertlog);

                //insert ke reedplate temp
                $insertemp = 'INSERT INTO reedplate_temps (major ,minor ,reader ,distance, mulai) 
                VALUES ('.$data['major'].', '.$data['minor'].',"'.$data['reader'].'",'.$data['distance'].',now()) ON DUPLICATE KEY UPDATE reader = VALUES(reader), distance = VALUES(distance)';

                $reed = DB::select($insertemp);
               
                //update to null semua ketika selesai ada isinya
                //reedplate_temps.reader = null
                $updatenull = 'UPDATE reedplate_temps SET reedplate_temps.mulai = null, reedplate_temps.selesai = null WHERE selesai is not null';

                $updaten = DB::select($updatenull);

                //update mulai ketika melebihi distance

                $updatemulai = 'UPDATE reedplate_temps AS a INNER JOIN reedplate_distances AS b ON a.reader = b.reader SET a.mulai = now() WHERE major='.$data['major'].' and minor='.$data['minor'].' and a.distance < b.distance and a.mulai is null';

                $updatem = DB::select($updatemulai);
            }

            $response = array(
                'status' => true,
                'message' => 'Berhasil Insert'
            );
            return Response::json($response);
        }
        catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

  

}


