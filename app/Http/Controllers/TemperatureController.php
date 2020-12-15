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
use App\IvmsTemperatureTemp;
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
           })->toObject();

            // var_dump($rows[0][1]);
             $person = [];

             $persondata = [];

             $index1 = 0;

             for ($i=0; $i < count($rows); $i++) {
               if ($rows[$i][1] == 'Face Authentication Passed') {
                    if ($rows[$i][4] != '-') {
                         $empid = explode(' ', $rows[$i][2]);
                         $temps = explode('°', $rows[$i][4]);

                         $personid = DB::SELECT('SELECT
                                    *,employee_groups.group as geroups
                             FROM
                             employee_groups
                             JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
                             WHERE
                             employee_groups.employee_id = "'.$empid[0].'"');

                         foreach ($personid as $key) {
                              $person_id = $key->geroups;
                              $name = $key->name;
                         }

                         $ivms = IvmsTemperatureTemp::create([
                              'person_id' => $person_id,
                              'employee_id' => $empid[0],
                              'name' => $name,
                              'location' => $request->get('location'),
                              'date' => date('Y-m-d', strtotime($rows[$i][6])),
                              'date_in' => $rows[$i][6],
                              'point' => $rows[$i][8],
                              'temperature' => $temps[0],
                              'abnormal_status' => $rows[$i][5],
                              'created_by' => $id_user,
                         ]);
                    }

                    // if (in_array($rows[$i][2], $person)) {

                    // }else{
                    //      if ($rows[$i][4] != '-') {
                    //           $temps = explode('°', $rows[$i][4]);

                    //           $empid = explode(' ', $rows[$i][2]);

                    //           $person[] = $rows[$i][2];
                    //           $personid = DB::SELECT('SELECT
                    //                *,employee_groups.group as geroups
                    //           FROM
                    //                employee_groups
                    //                JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
                    //           WHERE
                    //                employee_groups.employee_id = "'.$empid[0].'"');
                    //           foreach ($personid as $key) {
                    //                $person_id = $key->geroups;
                    //                $name = $key->name;
                    //           }

                    //           $persondata[$i][]
                    //           // $ivms = IvmsTemperature::firstOrNew(['employee_id' => $empid[0], 'date' => date('Y-m-d', strtotime($rows[$i][6]))]);
                    //           // $ivms->person_id = $person_id;
                    //           // $ivms->employee_id = $empid[0];
                    //           // $ivms->name = $name;
                    //           // $ivms->location = $request->get('location');
                    //           // $ivms->date = date('Y-m-d', strtotime($rows[$i][6]));
                    //           // $ivms->date_in = $rows[$i][6];
                    //           // $ivms->point = $rows[$i][8];
                    //           // $ivms->temperature = $temps[0];
                    //           // $ivms->abnormal_status = $rows[$i][5];
                    //           // $ivms->created_by = $id_user;
                    //           // $ivms->save();
                    //      }
                    // }
               }
          }

          $ivmstemp = DB::SELECT("SELECT DISTINCT ( a.employee_id ), name, person_id,( SELECT MAX( temperature ) FROM ivms_temperature_temps WHERE employee_id = a.employee_id ) AS temperature,
               ( SELECT MIN( date_in ) FROM ivms_temperature_temps WHERE employee_id = a.employee_id ) AS date_in,
               location,
               point,
               abnormal_status ,
               date
               FROM
               `ivms_temperature_temps` AS a");

          foreach ($ivmstemp as $key) {
               $ivms = IvmsTemperature::firstOrNew(['employee_id' => $key->employee_id, 'date' => $key->date]);
               $ivms->person_id = $key->person_id;
               $ivms->employee_id = $key->employee_id;
               $ivms->name = $key->name;
               $ivms->location = $key->location;
               $ivms->date = $key->date;
               $ivms->date_in = $key->date_in;
               $ivms->point = $key->point;
               $ivms->temperature = $key->temperature;
               $ivms->abnormal_status = $key->abnormal_status;
               $ivms->created_by = $id_user;
               $ivms->save();
          }

          IvmsTemperatureTemp::truncate();

          $miraimobile =DB::SELECT("SELECT *,miraimobile.quiz_logs.created_at as date_in FROM employee_groups join miraimobile.quiz_logs on employee_groups.employee_id = miraimobile.quiz_logs.employee_id where location = 'YMPI-OFFICE' and miraimobile.quiz_logs.answer_date = '".date('Y-m-d')."' and miraimobile.quiz_logs.question = 'Suhu Tubuh'");

            // var_dump($miraimobile);

          foreach ($miraimobile as $val) {
               $ivms = IvmsTemperature::firstOrNew(['employee_id' => $val->employee_id, 'date' => $val->answer_date]);
               $ivms->person_id = $val->group;
               $ivms->employee_id = $val->employee_id;
               $ivms->name = $val->name;
               $ivms->location = $val->location;
               $ivms->date = $val->answer_date;
               $ivms->date_in = $val->date_in;
               $ivms->point = "Mirai Mobile";
               $tempmobile = floatval($val->answer);
               $ivms->temperature = $tempmobile;
               if ($tempmobile > '37.5') {
                    $ivms->abnormal_status = "Yes";
               }else{
                    $ivms->abnormal_status = "No";
               }
               $ivms->created_by = $id_user;
               $ivms->save();
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
          $loc = 'OFC';
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

     $datatoday = DB::SELECT("SELECT DISTINCT ( a.temperature ),( SELECT count( person_id ) FROM ivms_temperatures WHERE DATE( date_in ) = '".$now."' AND temperature = a.temperature ) AS jumlah 
          FROM
          `ivms_temperatures` AS a 
          WHERE
          DATE( a.date_in ) = '".$now."' 
          AND 
          location = '".$request->get('location')."'
          ORDER BY
          a.temperature asc");

     if ($request->get('location') == 'OFC') {
          $employee_groups = DB::SELECT("SELECT employee_id from employees where remark = '".$request->get('location')."' or remark = 'Jps'");

          $attendance = [];

          foreach ($employee_groups as $key) {
               $attendance[] = DB::connection('sunfish')->select("SELECT
                    IIF (
                    Attend_Code LIKE '%Mangkir%',
                    'Mangkir',
                    IIF (
                    Attend_Code LIKE '%CK%' 
                    OR Attend_Code LIKE '%CUTI%' 
                    OR Attend_Code LIKE '%UPL%',
                    'Cuti',
                    IIF (
                    Attend_Code LIKE '%Izin%',
                    'Izin',
                    IIF (
                    Attend_Code LIKE '%SAKIT%',
                    'Sakit',
                    IIF ( Attend_Code LIKE '%LTI%' OR Attend_Code LIKE '%TELAT%', 'Terlambat', IIF ( Attend_Code LIKE '%LTI%', 'Pulang Cepat',
                    IIF ( Attend_Code LIKE '%PRS%', 'Present', shiftdaily_code ) ) )
                    ) 
                    ) 
                    ) 
                    ) as attend_code,
                    emp_no 
                    FROM
                    VIEW_YMPI_Emp_Attendance 
                    WHERE
                    Emp_no = '".$key->employee_id."'
                    AND FORMAT ( shiftstarttime, 'yyyy-MM-dd' ) = '".$now."'");
          }

          $datacheck = DB::SELECT("SELECT
               a.employee_id,
               a.name,(
               SELECT DISTINCT
                    (
                    IF
                         (
                              ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
                              SPLIT_STRING ( person_name, ' ', 1 ),
                         IF
                              (
                                   LENGTH( attend_id ) = 6,
                                   CONCAT( 'PI0', attend_id ),
                              IF
                                   (
                                        LENGTH( attend_id ) = 5,
                                        CONCAT( 'PI00', attend_id ),
                                   IF
                                        (
                                             LENGTH( attend_id ) = 4,
                                             CONCAT( 'PI000', attend_id ),
                                        IF
                                             (
                                                  LENGTH( attend_id ) = 3,
                                                  CONCAT( 'PI0000', attend_id ),
                                             IF
                                                  (
                                                       LENGTH( attend_id ) = 2,
                                                       CONCAT( 'PI00000', attend_id ),
                                                  IF
                                                       (
                                                            LENGTH( attend_id ) = 1,
                                                            CONCAT( 'PI000000', attend_id ),
                                                       CONCAT( 'PI', attend_id ))))))))) 
               FROM
                    ivms.ivms_attendance 
               WHERE
                    ivms.ivms_attendance.auth_date = '".$now."' 
               AND
               IF
                    (
                         ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
                         SPLIT_STRING ( person_name, ' ', 1 ),
                    IF
                         (
                              LENGTH( attend_id ) = 6,
                              CONCAT( 'PI0', attend_id ),
                         IF
                              (
                                   LENGTH( attend_id ) = 5,
                                   CONCAT( 'PI00', attend_id ),
                              IF
                                   (
                                        LENGTH( attend_id ) = 4,
                                        CONCAT( 'PI000', attend_id ),
                                   IF
                                        (
                                             LENGTH( attend_id ) = 3,
                                             CONCAT( 'PI0000', attend_id ),
                                        IF
                                             (
                                                  LENGTH( attend_id ) = 2,
                                                  CONCAT( 'PI00000', attend_id ),
                                             IF
                                                  (
                                                       LENGTH( attend_id ) = 1,
                                                       CONCAT( 'PI000000', attend_id ),
                                                  CONCAT( 'PI', attend_id )))))))) = a.employee_id 
               ) AS checks 
          FROM
               employees a 
          WHERE
               (a.remark = '".$request->get('location')."' 
               AND end_date IS NULL)
               OR
               (a.remark = 'Jps' 
               AND end_date IS NULL)");

          $dateTitle = date("d M Y", strtotime($now));

          $dataAbnormal = DB::SELECT("SELECT
               a.employee_id,
               a.name,(
               SELECT
                    MAX( ivms_temperatures.temperature ) 
               FROM
                    ivms_temperatures 
               WHERE
                    ivms_temperatures.date = '".$now."' 
                    AND employee_id = a.employee_id 
               ) AS temperature 
          FROM
               employees a 
          WHERE
               (a.remark = '".$request->get('location')."' 
               AND ( SELECT MAX( ivms_temperatures.temperature ) FROM ivms_temperatures WHERE ivms_temperatures.date = '".$now."' AND employee_id = a.employee_id ) >= 37.5)
               OR
               (a.remark = 'Jps' 
               AND ( SELECT MAX( ivms_temperatures.temperature ) FROM ivms_temperatures WHERE ivms_temperatures.date = '".$now."' AND employee_id = a.employee_id ) >= 37.5)");
     }else{
          $employee_groups = DB::SELECT("SELECT employee_id from employees where remark = '".$request->get('location')."'");

          $attendance = [];

          foreach ($employee_groups as $key) {
               $attendance[] = DB::connection('sunfish')->select("SELECT
                    IIF (
                    Attend_Code LIKE '%Mangkir%',
                    'Mangkir',
                    IIF (
                    Attend_Code LIKE '%CK%' 
                    OR Attend_Code LIKE '%CUTI%' 
                    OR Attend_Code LIKE '%UPL%',
                    'Cuti',
                    IIF (
                    Attend_Code LIKE '%Izin%',
                    'Izin',
                    IIF (
                    Attend_Code LIKE '%SAKIT%',
                    'Sakit',
                    IIF ( Attend_Code LIKE '%LTI%' OR Attend_Code LIKE '%TELAT%', 'Terlambat', IIF ( Attend_Code LIKE '%LTI%', 'Pulang Cepat',
                    IIF ( Attend_Code LIKE '%PRS%', 'Present', shiftdaily_code ) ) )
                    ) 
                    ) 
                    ) 
                    ) as attend_code,
                    emp_no 
                    FROM
                    VIEW_YMPI_Emp_Attendance 
                    WHERE
                    Emp_no = '".$key->employee_id."'
                    AND FORMAT ( shiftstarttime, 'yyyy-MM-dd' ) = '".$now."'");
          }

          $datacheck = DB::SELECT("SELECT
               a.employee_id,
               a.name,(
               SELECT DISTINCT
                    (
                    IF
                         (
                              ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
                              SPLIT_STRING ( person_name, ' ', 1 ),
                         IF
                              (
                                   LENGTH( attend_id ) = 6,
                                   CONCAT( 'PI0', attend_id ),
                              IF
                                   (
                                        LENGTH( attend_id ) = 5,
                                        CONCAT( 'PI00', attend_id ),
                                   IF
                                        (
                                             LENGTH( attend_id ) = 4,
                                             CONCAT( 'PI000', attend_id ),
                                        IF
                                             (
                                                  LENGTH( attend_id ) = 3,
                                                  CONCAT( 'PI0000', attend_id ),
                                             IF
                                                  (
                                                       LENGTH( attend_id ) = 2,
                                                       CONCAT( 'PI00000', attend_id ),
                                                  IF
                                                       (
                                                            LENGTH( attend_id ) = 1,
                                                            CONCAT( 'PI000000', attend_id ),
                                                       CONCAT( 'PI', attend_id ))))))))) 
               FROM
                    ivms.ivms_attendance 
               WHERE
                    ivms.ivms_attendance.auth_date = '".$now."' 
               AND
               IF
                    (
                         ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
                         SPLIT_STRING ( person_name, ' ', 1 ),
                    IF
                         (
                              LENGTH( attend_id ) = 6,
                              CONCAT( 'PI0', attend_id ),
                         IF
                              (
                                   LENGTH( attend_id ) = 5,
                                   CONCAT( 'PI00', attend_id ),
                              IF
                                   (
                                        LENGTH( attend_id ) = 4,
                                        CONCAT( 'PI000', attend_id ),
                                   IF
                                        (
                                             LENGTH( attend_id ) = 3,
                                             CONCAT( 'PI0000', attend_id ),
                                        IF
                                             (
                                                  LENGTH( attend_id ) = 2,
                                                  CONCAT( 'PI00000', attend_id ),
                                             IF
                                                  (
                                                       LENGTH( attend_id ) = 1,
                                                       CONCAT( 'PI000000', attend_id ),
                                                  CONCAT( 'PI', attend_id )))))))) = a.employee_id 
               ) AS checks 
          FROM
               employees a 
          WHERE
               a.remark = '".$request->get('location')."' 
               AND end_date IS NULL");

          $dateTitle = date("d M Y", strtotime($now));

          $dataAbnormal = DB::SELECT("SELECT
               a.employee_id,
               a.name,(
               SELECT
                    MAX( ivms_temperatures.temperature ) 
               FROM
                    ivms_temperatures 
               WHERE
                    ivms_temperatures.date = '".$now."' 
                    AND employee_id = a.employee_id 
               ) AS temperature 
          FROM
               employees a 
          WHERE
               a.remark = '".$request->get('location')."' 
               AND ( SELECT MAX( ivms_temperatures.temperature ) FROM ivms_temperatures WHERE ivms_temperatures.date = '".$now."' AND employee_id = a.employee_id ) >= 37.5");
     }

     $response = array(
          'status' => true,
          'message' => 'Get Data Success',
          'datatoday' => $datatoday,
          'dateTitle' => $dateTitle,
          'datacheck' => $datacheck,
          'dataAbnormal' => $dataAbnormal,
          'attendance' => $attendance,
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
