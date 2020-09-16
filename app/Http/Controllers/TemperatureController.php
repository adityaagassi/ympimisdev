<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Plc;
use App\standart_temperature;
use App\BodyTemperature;
use App\IvmsTemperature;
use Response;
use DataTables;
use App\Libraries\ActMLEasyIf;
use Excel;
use File;

class TemperatureController extends Controller
{

     public function RoomTemperature(){
          return view('temperature.temperatureMap', array(
               'title' => 'Room Temperature Map',
               'title_jp' => '室内温度のマップ',
          ))->with('page', 'Room Temperature');
     }

     public function indexOmron($id){
          $op_data = "-";

          $op_data = db::connection('omron'.$id)->table('op_data')->first();

          if(count($op_data) > 0){
               $employee = db::table('employees')->where('tag', '=', $op_data->tag)->first();               
          }
          else{
               $employee = '-';
          }

          if($id == 1){
               return view('temperature.omron1', array(
                    'title' => 'Self Check Body Temperature',
                    'title_jp' => '',
                    'employee' => $employee,
               ))->with('page', 'Self Check Body Temperature');
          }
          if($id == 2){
               return view('temperature.omron2', array(
                    'title' => 'Self Check Body Temperature',
                    'title_jp' => '',
                    'employee' => $employee,
               ))->with('page', 'Self Check Body Temperature');
          }
          if($id == 3){
               return view('temperature.omron3', array(
                    'title' => 'Self Check Body Temperature',
                    'title_jp' => '',
                    'employee' => $employee,
               ))->with('page', 'Self Check Body Temperature');
          }
     }

     public function fetchOmron(Request $request){

          $calibration = $request->get('calibration');
          $suhu = 0;
          if($request->get('tag') != "" && $request->get('tag') != "-"){
               $omron = db::connection('omron'.$request->get('id'))->table('log_data')->orderBy('created', 'desc')->first();
               if(count($omron) > 0 ){
                    $suhu = $omron->suhu-$calibration;
               }
               if($suhu >= 20){
                    $op_log_data = db::connection('omron'.$request->get('id'))->table('op_log_data')->insert([
                         'tag' => $request->get('tag'),
                         'temperature' => $suhu,
                         'created_at' => date('Y-m-d H:i:s'),
                    ]); 
               }
               $response = array(
                    'status' => true,
                    'suhu' => $suhu
               );
               return Response::json($response);
          }
          $response = array(
               'status' => true,
               'suhu' => $suhu,
               'message' => 'Tidak ada login'
          );
          return Response::json($response);

     }
     
     public function inputOmronOperator(Request $request){

          $employee = db::table('employees')->where('tag', '=', $request->get('tag'))->first();

          if(count($employee) <= 0){
               $response = array(
                    'status' => false,
                    'message' => 'Tag karyawan tidak terdaftar',
               );
               return Response::json($response);
          }

          $op_data = db::connection('omron'.$request->get('id'))->table('op_data')->first();

          if(count($op_data) > 0 ){
               $cat = 'logout';
               $trun_op_data = db::connection('omron'.$request->get('id'))->table('op_data')->truncate();
          }
          else{
               $cat = 'login';
               $op = db::connection('omron'.$request->get('id'))->table('op_data')->insert([
                    'tag' => $request->get('tag')
               ]);
          }

          $log = db::connection('omron'.$request->get('id'))->select("SELECT tag, max(temperature) as temperature FROM `op_log_data` group by tag having tag <> '-' and temperature > 0");

          foreach ($log as $val) {
               $mirai = db::table('temperature_body_logs')->insert([
                    'tag' => $val->tag,
                    'temperature' => $val->temperature,
                    'created_at' => date('Y-m-d H:i:s'),
                    'deleted_at' => date('Y-m-d H:i:s'),
               ]);
          }

          $trun_op_log_data = db::connection('omron'.$request->get('id'))->table('op_log_data')->truncate();
          $trun_log_data = db::connection('omron'.$request->get('id'))->table('log_data')->truncate();

          $response = array(
               'status' => true,
               'cat' => $cat,
               'employee' => $employee
          );
          return Response::json($response);
     }

     public function fetchRoomTemperature(Request $request){
          $plcs = Plc::orderBy('location', 'asc')->get();
          $lists = array();

          foreach ($plcs as $plc) {
               $cpu = new ActMLEasyIf($plc->station);
               $datas = $cpu->read_data($plc->address, 10);
               $data = $datas[$plc->arr];

               array_push($lists, [
                    'location' => $plc->location,
                    'remark' => $plc->remark,
                    'value' => $data,
                    'upper_limit' => $plc->upper_limit,
                    'lower_limit' => $plc->lower_limit
               ]);
          }

          $response = array(
               'status' => true,
               'lists' => $lists,
          );
          return Response::json($response);
     }

     public function index()
     {
          return view('temperature.index', array(
               'title' => 'Temperature',
               'title_jp' => '温度'
          ))->with('page', 'Temperature');
     }

     public function indexBodyTemperatureReport()
     {
          return view('temperature.index_b_temp_report', array(
               'title' => 'Body Temperature Report',
               'title_jp' => '体温リポート'
          ))->with('page', 'Body Temperature Report');
     }

     public function fetchBodyTemperatureReport(Request $request)
     {
          $date_from = $request->get('tanggal_from');
          $date_to = $request->get('tanggal_to');
          if ($date_from == '') {
               if ($date_to == '') {
                    $where = "WHERE DATE(created_at) BETWEEN CONCAT(DATE_FORMAT(NOW(),'%Y-%m-01')) AND DATE(NOW())";
               }else{
                    $where = "WHERE DATE(created_at) BETWEEN CONCAT(DATE_FORMAT(NOW(),'%Y-%m-01')) AND '".$date_to."'";
               }
          }else{
               if ($date_to == '') {
                    $where = "WHERE DATE(created_at) BETWEEN '".$date_from."' AND DATE(NOW())";
               }else{
                    $where = "WHERE DATE(created_at) BETWEEN '".$date_from."' AND '".$date_to."'";
               }
          }

          $temperature = DB::SELECT("SELECT
               *,
               DATE( created_at ) AS tanggal 
               FROM
               `body_temperatures`
               ".$where."");

          $response = array(
               'status' => true,
               'datas' => $temperature
          );

          return Response::json($response);

     }

     public function indexBodyTempMonitoring()
     {
          return view('temperature.index_b_temp_monitoring', array(
               'title' => 'Body Temperature Monitoring',
               'title_jp' => '体温監視'
          ))->with('page', 'Body Temperature Monitoring');
     }

     public function fetchBodyTempMonitoring(Request $request)
     {
          $date_from = $request->get('tanggal_from');
          $date_to = $request->get('tanggal_to');
          if ($date_from == '') {
               if ($date_to == '') {
                    $where = "AND week_date BETWEEN CONCAT(DATE_FORMAT(NOW(),'%Y-%m-01')) AND DATE(NOW())";
               }else{
                    $where = "AND week_date BETWEEN CONCAT(DATE_FORMAT(NOW(),'%Y-%m-01')) AND '".$date_to."'";
               }
          }else{
               if ($date_to == '') {
                    $where = "AND week_date BETWEEN '".$date_from."' AND DATE(NOW())";
               }else{
                    $where = "AND week_date BETWEEN '".$date_from."' AND '".$date_to."'";
               }
          }
          $temp = DB::SELECT("SELECT
               DATE_FORMAT(week_date,'%d %b %Y') as week_date,
               ( SELECT count( id ) AS total FROM body_temperatures WHERE DATE( created_at ) = week_date ) AS total,
               ( SELECT ROUND( AVG( suhu ), 1 ) AS avg FROM body_temperatures WHERE DATE( created_at ) = week_date ) AS avg,
               ( SELECT ROUND( MAX( suhu ), 1 ) AS max FROM body_temperatures WHERE DATE( created_at ) = week_date ) AS highest 
               FROM
               weekly_calendars 
               WHERE remark != 'H' 
               AND week_date IN ( SELECT DATE( created_at ) AS date FROM body_temperatures ) 
               ".$where."");

          $temp_now = DB::SELECT("SELECT
               DATE_FORMAT(week_date,'%d %b %Y') as week_date,
               ( SELECT count( id ) AS total FROM body_temperatures WHERE DATE( created_at ) = week_date ) AS total,
               ( SELECT ROUND( AVG( suhu ), 1 ) AS avg FROM body_temperatures WHERE DATE( created_at ) = week_date ) AS avg,
               ( SELECT ROUND( MAX( suhu ), 1 ) AS max FROM body_temperatures WHERE DATE( created_at ) = week_date ) AS highest 
               FROM
               weekly_calendars 
               WHERE
               weekly_calendars.week_date BETWEEN CONCAT( YEAR ( NOW()), '-', MONTH ( NOW()), '-01' ) 
               AND DATE(
               NOW()) 
               AND remark != 'H' 
               AND week_date IN ( SELECT DATE( created_at ) AS date FROM body_temperatures ) 
               AND week_date = DATE(NOW())");

          $response = array(
               'status' => true,
               'datas' => $temp,
               'datas_now' => $temp_now
          );

          return Response::json($response);
     }

     public function indexMinMoe($location)
     {
          if ($location == 'office') {
               $loc = 'YMPI-OFFICE';
          }
          return view('temperature.index_minmoe', array(
               'loc' => $loc,
               'location' => $location
          ))->with('page', 'Temperature');
     }

     public function fetchMinMoe(Request $request)
     {
          try {
               $date_from = $request->get('tanggal_from');
               $date_to = $request->get('tanggal_to');

               if ($date_from == '') {
                    if ($date_to == '') {
                         $where = "AND DATE(date_in) BETWEEN CONCAT(DATE_FORMAT(NOW() - INTERVAL 7 DAY,'%Y-%m-%d')) AND DATE(NOW())";
                    }else{
                         $where = "AND DATE(date_in) BETWEEN CONCAT(DATE_FORMAT(".$date_to." - INTERVAL 7 DAY,'%Y-%m-%d')) AND '".$date_to."'";
                    }
               }else{
                    if ($date_to == '') {
                         $where = "AND DATE(date_in) BETWEEN '".$date_from."' AND DATE(NOW())";
                    }else{
                         $where = "AND DATE(date_in) BETWEEN '".$date_from."' AND '".$date_to."'";
                    }
               }

               $minmoeall = DB::SELECT('SELECT person_id,employee_syncs.employee_id,ivms_temperatures.location,employee_syncs.name,ivms_temperatures.date_in,ivms_temperatures.point,ivms_temperatures.temperature,ivms_temperatures.abnormal_status from ivms_temperatures left join employee_groups on employee_groups.group = ivms_temperatures.person_id left join employee_syncs on employee_groups.employee_id = employee_syncs.employee_id where employee_groups.location = "'.$request->get('location').'" '.$where.' order by date_in desc');

               $response = array(
                    'status' => true,
                    'message' => 'Get Data Success',
                    'datas' => $minmoeall
               );

               return Response::json($response);
          } catch (\Exception $e) {
               $response = array(
                    'status' => false,
                    'message' => 'Get Data Failed'
               );

               return Response::json($response);
          }
     }

     public function importMinMoe(Request $request)
    {
          try{

            $id_user = Auth::id();

            $file = $request->file('file');
            $file_name = 'temp_'. MD5(date("YmdHisa")) .'.'.$file->getClientOriginalExtension();
            $file->move('data_file/temperature/minmoe/', $file_name);

            $excel = 'data_file/temperature/minmoe/' . $file_name;
            $rows = Excel::load($excel, function($reader) {
              $reader->noHeading();
              $reader->skipRows(1);

              $reader->each(function($row) {
              });
            })->toObject()->groupBy('Person ID');

            $person_id = [];

            $data = [];

            for ($i=0; $i < count($rows); $i++) {
               $exprow = explode(',', $rows[$i][0]);

               $expperid = explode("'", $exprow[0]);

               $points = explode('_', $exprow[5]);

               if ($exprow[8] != '-') {
                    $temps = explode('°', $exprow[8]);
               }

               if (in_array($expperid[1], $person_id)) {
                    
               }else{
                    if ($exprow[8] != '-') {
                         $person_id[] = $expperid[1];
                         IvmsTemperature::create([
                              'person_id' => $expperid[1],
                              'name' => $exprow[1],
                              'location' => $exprow[2],
                              'date_in' => $exprow[3],
                              'point' => $points[0],
                              'temperature' => $temps[0],
                              'abnormal_status' => $exprow[9],
                              'created_by' => $id_user
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

    public function indexMinMoeMonitoring($location)
    {

          $title = "Resume Pengecekan Suhu Tubuh Karyawan";
          $title_jp = "従業員の検温のまとめ";

         if ($location == 'office') {
               $loc = 'YMPI-OFFICE';
          }
          return view('temperature.minmoe_monitoring', array(
               'loc' => $loc,
               'location' => $location,
               'title' => $title,
               'title_jp' => $title_jp
          ))->with('page', 'Temperature');
    }

    public function fetchMinMoeMonitoring(Request $request)
    {
         try {
               $date_from = $request->get('tanggal_from');
               // $date_to = $request->get('tanggal_to');
               $now  = date('Y-m-d');

               if ($date_from != null) {
                    $now  = $date_from;
               }

               // if ($date_from == '') {
               //      if ($date_to == '') {
               //           $where = "WHERE DATE(a.date_in) BETWEEN CONCAT(DATE_FORMAT(NOW() - INTERVAL 7 DAY,'%Y-%m-%d')) AND DATE(NOW())";
               //      }else{
               //           $where = "WHERE DATE(a.date_in) BETWEEN CONCAT(DATE_FORMAT(".$date_to." - INTERVAL 7 DAY,'%Y-%m-%d')) AND '".$date_to."'";
               //      }
               // }else{
               //      if ($date_to == '') {
               //           $where = "WHERE DATE(a.date_in) BETWEEN '".$date_from."' AND DATE(NOW())";
               //      }else{
               //           $where = "WHERE DATE(a.date_in) BETWEEN '".$date_from."' AND '".$date_to."'";
               //      }
               // }

               $datatoday = DB::SELECT("SELECT DISTINCT ( a.temperature ),( SELECT count( person_id ) FROM ivms_temperatures WHERE DATE( date_in ) = '".$now."' AND temperature = a.temperature ) AS jumlah 
                    FROM
                         `ivms_temperatures` AS a 
                    WHERE
                         DATE( a.date_in ) = '".$now."' 
                    AND 
                         location = '".$request->get('location')."'
                    ORDER BY
                         a.temperature DESC");

               // $dataavgmax = DB::SELECT("SELECT DISTINCT
               //      (
               //           date( a.date_in )) AS date,(
               //      SELECT
               //           ROUND( AVG( temperature ), 1 ) 
               //      FROM
               //           ivms_temperatures 
               //      WHERE
               //      DATE( date_in ) = DATE( a.date_in )) AS average,
               //      (
               //      SELECT
               //           ROUND( MAX( temperature ), 1 ) 
               //      FROM
               //           ivms_temperatures 
               //      WHERE
               //      DATE( date_in ) = DATE( a.date_in )) AS highest 
               // FROM
               //      ivms_temperatures AS a 
               // ".$where."");

               $datacheck = DB::SELECT("SELECT
                    employee_groups.employee_id,
                    employee_syncs.name,
                    employee_groups.group,(
                    SELECT
                         ivms_temperatures.person_id 
                    FROM
                         ivms_temperatures 
                    WHERE
                         DATE( date_in ) = '".$now."' 
                         AND person_id = employee_groups.GROUP 
                    ) AS checks 
               FROM
                    employee_groups
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
               WHERE
                    employee_groups.location = '".$request->get('location')."'
               ORDER BY employee_groups.group");

               $response = array(
                    'status' => true,
                    'message' => 'Get Data Success',
                    'datatoday' => $datatoday,
                    // 'dataavgmax' => $dataavgmax,
                    'datacheck' => $datacheck,
               );

               return Response::json($response);
          } catch (\Exception $e) {
               $response = array(
                    'status' => false,
                    'message' => 'Get Data Failed'
               );

               return Response::json($response);
          }
    }

    public function fetchDetailMinMoeMonitoring(Request $request)
    {
         try {
               $date_from = $request->get('tanggal_from');
               $now  = date('Y-m-d');

               if ($date_from != null) {
                    $now  = $date_from;
               }

               $temperature = $request->get('temperature');

               // var_dump(str_replace('.', ',', $temperature));
               // var_dump($now);
               // var_dump($request->get('location'));

               $detail = DB::SELECT("SELECT
                    * 
               FROM
                    ivms_temperatures 
                    LEFT JOIN employee_groups ON employee_groups.GROUP = ivms_temperatures.person_id
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
               WHERE
                    employee_groups.location = '".$request->get('location')."' 
                    AND DATE( date_in ) = '".$now."' 
                    AND temperature = ".$temperature."");


               $response = array(
                    'status' => true,
                    'message' => 'Get Data Success',
                    'details' => $detail
               );

               return Response::json($response);
          } catch (\Exception $e) {
               $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
               );

               return Response::json($response);
          }
    }
}
