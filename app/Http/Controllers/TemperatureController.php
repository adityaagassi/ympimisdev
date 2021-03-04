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
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

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
          $department = DB::SELECT('SELECT DISTINCT
               ( department ) as department_name,
               department_shortname 
          FROM
               employee_syncs
               JOIN employees ON employees.employee_id = employee_syncs.employee_id
               JOIN departments ON departments.department_name = employee_syncs.department 
          WHERE
               (department IS NOT NULL 
               AND remark != "OFC" 
               AND remark != "Jps" 
               AND id_division = 5)
               OR
               (department IS NOT NULL 
               AND remark != "OFC" 
               AND remark != "Jps" and
               department_shortname = "GA")
               OR
               (department IS NOT NULL 
               AND remark != "OFC" 
               AND remark != "Jps" and
               department_shortname = "LOG")');

          return view('temperature.index', array(
               'title' => 'Temperature',
               'title_jp' => '温度',
               'department' => $department
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

     public function indexMinMoe()
     {
          return view('temperature.index_minmoe', array(
          ))->with('page', 'Temperature');
     }

     public function fetchMinMoe(Request $request)
     {
          try {
               $date_from = $request->get('tanggal_from');
               $date_to = $request->get('tanggal_to');
               $now = date('Y-m-d');

               if ($date_from == '') {
                    if ($date_to == '') {
                         $whereDate = 'AND DATE(date_in)  = "'.$now.'"';
                    }else{
                         $whereDate = 'AND DATE(date_in) BETWEEN CONCAT(DATE_FORMAT("'.$date_to.'" - INTERVAL 4 DAY,"%Y-%m-%d")) AND "'.$date_to.'"';
                    }
               }else{
                    if ($date_to == '') {
                         $whereDate = 'AND DATE(date_in) BETWEEN "'.$date_from.'" AND DATE(NOW())';
                    }else{
                         $whereDate = 'AND DATE(date_in) BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
                    }
               }

               $temp_from = $request->get('temp_from');
               $temp_to = $request->get('temp_to');

               if ($temp_from == '') {
                    if ($temp_to == '') {
                         $whereTemp = '';
                    }else{
                         $whereTemp = 'AND temperature <= "'.$temp_to.'"';
                    }
               }else{
                    if ($temp_to == '') {
                         $whereTemp = 'AND temperature >= "'.$temp_from.'"';
                    }else{
                         $whereTemp = 'AND temperature BETWEEN "'.$temp_from.'" AND "'.$temp_to.'"';
                    }
               }

               $minmoeall = DB::SELECT('SELECT
                         employees.employee_id,
                         ivms_temperatures.location,
                         employees.name,
                         COALESCE ( employee_syncs.department, "" ) as department,
                         COALESCE ( employee_syncs.section, "" ) as section,
                         COALESCE ( employee_syncs.`group`, "" ) as `group`,
                         ivms_temperatures.date_in,
                         ivms_temperatures.point,
                         ivms_temperatures.temperature,
                         ivms_temperatures.abnormal_status,
                         ivms_temperatures.shift
                    FROM
                         ivms_temperatures
                         LEFT JOIN employees ON ivms_temperatures.employee_id = employees.employee_id
                         LEFT JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id 
                    WHERE
                         employee_syncs.end_date IS NULL 
                         '.$whereDate.'
                         '.$whereTemp.'
                    ORDER BY
                         date_in DESC');


               $response = array(
                    'status' => true,
                    'message' => 'Get Data Success',
                    'datas' => $minmoeall
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

//      public function importMinMoe(Request $request)
//      {
//           try{

//              $now = date('Y-m-d');
//              $yesterday = date('Y-m-d', strtotime('-1 days', strtotime($now)));
//              $tomorrow = date('Y-m-d', strtotime('+1 days', strtotime($now)));

//              $id_user = Auth::id();

//              $file = $request->file('file');
//              $file_name = 'temp_'. MD5(date("YmdHisa")) .'.'.$file->getClientOriginalExtension();
//              $file->move('data_file/temperature/minmoe/', $file_name);

//              $excel = 'data_file/temperature/minmoe/' . $file_name;
//              $rows = Excel::load($excel, function($reader) {
//                 $reader->noHeading();
//                 $reader->skipRows(1);

//                 $reader->each(function($row) {
//                 });
//            })->toObject();

//              $person = [];

//              $persondata = [];

//              $index1 = 0;

//              $suhu = [];

//              for ($i=0; $i < count($rows); $i++) {
//                if ($rows[$i][1] == 'Face Authentication Passed') {
//                     if ($rows[$i][4] != '-') {
//                          $temps = explode('°', $rows[$i][4]);

//                          if (str_contains($rows[$i][2], "''")) {
//                               $empname = str_replace("''","'",$rows[$i][2]);
//                          }else{
//                               $empname = $rows[$i][2];
//                          }


//                          $empys = DB::SELECT('SELECT
//                                    * 
//                               FROM
//                                    employees
//                                    JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id 
//                                    JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = employees.employee_id 
//                               WHERE
//                                    employees.name = "'.$empname.'"
//                                    AND sunfish_shift_syncs.shift_date = "'.date('Y-m-d', strtotime($rows[$i][6])).'"');

//                          if (count($empys) > 0) {
//                               foreach ($empys as $key) {
//                                    $employee_id = $key->employee_id;
//                                    $shiftdaily_code = $key->shiftdaily_code;
//                                    $name = $key->name;
//                                    if ($key->department == null) {
//                                         $department = '';
//                                    }else{
//                                         $department = $key->department;
//                                    }
//                                    if ($key->section == null) {
//                                         $section = '';
//                                    }else{
//                                         $section = $key->section;
//                                    }
//                                    if ($key->group == null) {
//                                         $group = '';
//                                    }else{
//                                         $group = $key->group;
//                                    }
//                               }

//                               $ivms = IvmsTemperatureTemp::create([
//                                    'employee_id' => $employee_id,
//                                    'name' => $name,
//                                    'department' => $department,
//                                    'section' => $section,
//                                    'group' => $group,
//                                    'date' => date('Y-m-d', strtotime($rows[$i][6])),
//                                    'date_in' => $rows[$i][6],
//                                    'point' => $rows[$i][9],
//                                    'temperature' => $temps[0],
//                                    'abnormal_status' => $rows[$i][5],
//                                    'shiftdaily_code' => $shiftdaily_code,
//                                    'created_by' => $id_user,
//                               ]);
//                          }else{
//                               $empys = DB::SELECT('SELECT
//                                    * 
//                               FROM
//                                    employees
//                                    JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id 
//                                    JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = employees.employee_id 
//                               WHERE
//                                    employees.name LIKE "%'.$empname.'%"
//                                    AND sunfish_shift_syncs.shift_date = "'.date('Y-m-d', strtotime($rows[$i][6])).'"');

//                               if (count($empys) > 0) {
//                                    foreach ($empys as $key) {
//                                         $shiftdaily_code = $key->shiftdaily_code;
//                                         $employee_id = $key->employee_id;
//                                         $name = $key->name;
//                                         $department = $key->department;
//                                         $section = $key->section;
//                                         $group = $key->group;
//                                    }

//                                    $ivms = IvmsTemperatureTemp::create([
//                                         'employee_id' => $employee_id,
//                                         'name' => $name,
//                                         'department' => $department,
//                                         'section' => $section,
//                                         'group' => $group,
//                                         'date' => date('Y-m-d', strtotime($rows[$i][6])),
//                                         'date_in' => $rows[$i][6],
//                                         'point' => $rows[$i][9],
//                                         'temperature' => $temps[0],
//                                         'abnormal_status' => $rows[$i][5],
//                                         'shiftdaily_code' => $shiftdaily_code,
//                                         'created_by' => $id_user,
//                                    ]);
//                               }
//                          }
//                     }
//                }
//           }

//           $IvmsTemperature = DB::SELECT("SELECT DISTINCT
//                ( a.employee_id ),
//                name,
//                point,
//                shiftdaily_code,
//                abnormal_status,
//                department,
//                section,
//                `group`,
//                date 
//           FROM
//                `ivms_temperature_temps` AS a");

//           foreach ($IvmsTemperature as $key) {
//                $gettime = DB::SELECT("SELECT 
//                     '".$key->employee_id."' as employee_id,
//                     IF
//                          (
//                               '".$key->shiftdaily_code."' LIKE '%Shift_3%',
//                               (
//                               SELECT
//                                    MAX( date_in ) 
//                               FROM
//                                    ivms_temperature_temps
//                               WHERE
//                                    date = DATE( '".$key->date."' ) - INTERVAL 1 DAY 
//                                    AND ivms_temperature_temps.employee_id = '".$key->employee_id."'
//                               ),
//                          IF
//                               (
//                                    '".$key->shiftdaily_code."' LIKE '%Shift_2%',(
//                                    SELECT
//                                         min( date_in ) 
//                                    FROM
//                                         ivms_temperature_temps 
//                                    WHERE
//                                          date = '".$key->date."' 
//                                         AND ivms_temperature_temps.employee_id = '".$key->employee_id."'
//                                         AND date_in BETWEEN '".$key->date." 14:00:00' 
//                                         AND '".$key->date." 17:00:00' 
//                                         ),(
//                                    SELECT
//                                         min( date_in ) 
//                                    FROM
//                                         ivms_temperature_temps 
//                                    WHERE
//                                          date = '".$key->date."' 
//                                         AND ivms_temperature_temps.employee_id = '".$key->employee_id."'
//                                    ) 
//                               ) 
//                          ) AS time_in,
//                     IF
//                          (
//                               '".$key->shiftdaily_code."' LIKE '%Shift_3%',
//                               (
//                               SELECT
//                                    MIN( date_in ) 
//                               FROM
//                                    ivms_temperature_temps
//                               WHERE date = '".$key->date."'
//                                    AND ivms_temperature_temps.employee_id = '".$key->employee_id."'
//                               ),
//                          IF
//                               (
//                                    '".$key->shiftdaily_code."' LIKE '%Shift_2%',(
//                                    SELECT
//                                         min( date_in ) 
//                                    FROM
//                                         ivms_temperature_temps 
//                                    WHERE
//                                          date = '".$key->date."' + INTERVAL 1 DAY
//                                         AND ivms_temperature_temps.employee_id = '".$key->employee_id."'
//                                         ),IF('".$key->shiftdaily_code."' LIKE '%Shift_1%',
//                                         (SELECT
//                                         MAX( date_in ) 
//                                    FROM
//                                         ivms_temperature_temps 
//                                    WHERE
//                                          date = '".$key->date."'
//                                         AND ivms_temperature_temps.employee_id = '".$key->employee_id."'
//                                         AND date_in >='".$key->date." 15:00:00'  ),
//                                         (SELECT
//                                         MAX( date_in ) 
//                                    FROM
//                                         ivms_temperature_temps 
//                                    WHERE
//                                          date = '".$key->date."'
//                                         AND ivms_temperature_temps.employee_id = '".$key->employee_id."'   )) 
//                               ) 
//                          ) AS time_out");

//                $time_in = null;
//                $time_out = null;
//                $tempin = null;
//                $tempout = null;

//                foreach ($gettime as $vel) {
//                     $time_in = $vel->time_in;
//                     $time_out = $vel->time_out;
//                     if ($time_in != null) {
//                          $gettempin = DB::SELECT("SELECT
//                                    MAX( temperature ) AS temp 
//                               FROM
//                                    ivms_temperature_temps 
//                               WHERE
//                                    employee_id = '".$key->employee_id."' 
//                                    AND date_in BETWEEN '".$time_in."' - INTERVAL 30 MINUTE 
//                                    AND '".$time_in."' + INTERVAL 30 MINUTE");

//                          foreach ($gettempin as $temps) {
//                               $tempin = $temps->temp;
//                          }
//                     }
//                     if ($time_out != null) {
//                          $gettempout = DB::SELECT("SELECT
//                                    MAX( temperature ) AS temp 
//                               FROM
//                                    ivms_temperature_temps 
//                               WHERE
//                                    employee_id = '".$key->employee_id."' 
//                                    AND date_in BETWEEN '".$time_out."' - INTERVAL 30 MINUTE 
//                                    AND '".$time_out."' + INTERVAL 30 MINUTE");
//                          foreach ($gettempout as $temps) {
//                               $tempout = $temps->temp;
//                          }
//                     }
//                }
//                $ivmscheck = IvmsTemperature::where('employee_id',$key->employee_id)->where('date',$key->date)->first();
//                // $ivms = IvmsTemperature::firstOrNew(['employee_id' => $key->employee_id, 'date' => $key->date]);
//                // $ivms->employee_id = $key->employee_id;
//                // $ivms->name = $key->name;
//                // $ivms->date = $key->date;
//                // $ivms->date_in = $key->date_in;
//                // $ivms->point = $key->point;
//                // $ivms->temperature = $key->temperature;
//                // $ivms->abnormal_status = $key->abnormal_status;
//                // $ivms->created_by = $id_user;
//                // $ivms->save();

//                if (count($ivmscheck) == 0) {
//                     $ivms = IvmsTemperature::create([
//                          'employee_id' => $key->employee_id,
//                          'name' => $key->name,
//                          'date' => $key->date,
//                          'date_in' => $time_in,
//                          'temperature' => $tempin,
//                          'date_out' => $time_out,
//                          'temperature_out' => $tempout,
//                          'point' => $key->point,
//                          'abnormal_status' => $key->abnormal_status,
//                          'shiftdaily_code' => $key->shiftdaily_code,
//                          'created_by' => $id_user,
//                     ]);
//                     if ($tempin >= '37.5') {
//                          $suhutinggi = array(
//                               'employee_id' => $key->employee_id,
//                               'name' => $key->name,
//                               'date' => $key->date,
//                               'date_in' => $time_in,
//                               'point' => $key->point,
//                               'department' => $key->department,
//                               'section' => $key->section,
//                               'group' => $key->group,
//                               'shiftdaily_code' => $key->shiftdaily_code,
//                               'temperature' => $tempin,
//                          );
//                          array_push($suhu,$suhutinggi);
//                     }
//                     if ($tempout >= '37.5') {
//                          $suhutinggi = array(
//                               'employee_id' => $key->employee_id,
//                               'name' => $key->name,
//                               'date' => $key->date,
//                               'date_in' => $time_out,
//                               'point' => $key->point,
//                               'department' => $key->department,
//                               'section' => $key->section,
//                               'group' => $key->group,
//                               'shiftdaily_code' => $key->shiftdaily_code,
//                               'temperature' => $tempout,
//                          );
//                          array_push($suhu,$suhutinggi);
//                     }
//                }else{
//                     if ($ivmscheck->date_in == null) {
//                          $ivmscheck->date_in = $time_in;
//                          $ivmscheck->temperature = $tempin;
//                     }
//                     if ($ivmscheck->date_out == null) {
//                          $ivmscheck->date_out = $time_out;
//                          $ivmscheck->temperature_out = $tempout;
//                     }
//                     if ($tempin >= '37.5') {
//                          $suhutinggi = array(
//                               'employee_id' => $ivmscheck->employee_id,
//                               'name' => $ivmscheck->name,
//                               'date' => $ivmscheck->date,
//                               'date_in' => $time_in,
//                               'point' => $ivmscheck->point,
//                               'department' => $ivmscheck->department,
//                               'section' => $ivmscheck->section,
//                               'group' => $ivmscheck->group,
//                               'shiftdaily_code' => $ivmscheck->shiftdaily_code,
//                               'temperature' => $tempin,
//                          );
//                          array_push($suhu,$suhutinggi);
//                     }
//                     if ($tempout >= '37.5') {
//                          $suhutinggi = array(
//                               'employee_id' => $ivmscheck->employee_id,
//                               'name' => $ivmscheck->name,
//                               'date' => $ivmscheck->date,
//                               'date_in' => $time_out,
//                               'point' => $ivmscheck->point,
//                               'department' => $ivmscheck->department,
//                               'section' => $ivmscheck->section,
//                               'group' => $ivmscheck->group,
//                               'shiftdaily_code' => $ivmscheck->shiftdaily_code,
//                               'temperature' => $tempout,
//                          );
//                          array_push($suhu,$suhutinggi);
//                     }
//                     $ivmscheck->save();
//                }
//           }

//           // IvmsTemperatureTemp::truncate();
//           $miraimobile =DB::SELECT("SELECT
//                *,
//                miraimobile.quiz_logs.created_at AS date_in 
//           FROM
//                employees
//                JOIN miraimobile.quiz_logs ON employees.employee_id = miraimobile.quiz_logs.employee_id
//                JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id 
//           WHERE
//                employees.end_date IS NULL 
//                AND miraimobile.quiz_logs.answer_date = '".date('Y-m-d')."' 
//                AND miraimobile.quiz_logs.question = 'Suhu Tubuh'");
//           foreach ($miraimobile as $val) {
//                $ivmscheck = IvmsTemperature::where('employee_id',$val->employee_id)->where('date',$val->answer_date)->first();

//                // $ivms = IvmsTemperature::firstOrNew(['employee_id' => $val->employee_id, 'date' => $val->answer_date]);
//                // $ivms->employee_id = $val->employee_id;
//                // $ivms->name = $val->name;
//                // $ivms->date = $val->answer_date;
//                // $ivms->date_in = $val->date_in;
//                // $ivms->point = "Mirai Mobile";
//                // $tempmobile = floatval($val->answer);
//                // $ivms->temperature = $tempmobile;
//                // if ($tempmobile >= '37.5') {
//                //      $ivms->abnormal_status = "Yes";
//                // }else{
//                //      $ivms->abnormal_status = "No";
//                // }
//                // $ivms->created_by = $id_user;
//                // $ivms->save();

//                // if (count($ivmscheck) == 0) {
//                //      if ($tempmobile >= '37.5') {
//                //           $suhutinggi = array(
//                //                'employee_id' => $val->employee_id,
//                //                'name' => $val->name,
//                //                'date' => $val->answer_date,
//                //                'date_in' => $val->date_in,
//                //                'point' => "Mirai Mobile",
//                //                'department' => $val->department,
//                //                'section' => $val->section,
//                //                'group' => $val->group,
//                //                'temperature' => $tempmobile,
//                //           );
//                //           array_push($suhu,$suhutinggi);
//                //      }
//                // }
//           }

//           $contactList = [];
//           $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';

//           $contactList2 = [];
//           $contactList2[0] = 'prawoto@music.yamaha.com';
//           $contactList2[1] = 'putri.sukma.riyanti@music.yamaha.com';
//           $contactList2[2] = 'mahendra.putra@music.yamaha.com';

//           $mail_to = [];

//           for ($i = 0;$i < count($suhu); $i++) {
//                $fc = DB::SELECT("SELECT
//                     employee_id,
//                     employee_syncs.name,
//                     email 
//                FROM
//                     employee_syncs
//                     JOIN users ON users.username = employee_syncs.employee_id 
//                WHERE
//                     ( position LIKE '%Foreman%' AND department = '".$suhu[$i]['department']."' and email like '%music.yamaha.com%' and employee_syncs.end_date is null) 
//                     OR (
//                     position LIKE '%Chief%' 
//                     AND department = '".$suhu[$i]['department']."' and email like '%music.yamaha.com%' and employee_syncs.end_date is null)");


//                if (count($fc) > 0) {
//                     foreach ($fc as $val) {
//                          array_push($mail_to, $val->email);
//                     }
//                }
//           }

//           if (count($suhu) > 0) {
//                Mail::to($mail_to)->cc($contactList2,'CC')->bcc($contactList,'BCC')->send(new SendEmail($suhu, 'temperature'));
//           }

//           $response = array(
//            'status' => true,
//            'message' => 'Upload file success',
//       );
//           return Response::json($response);

//      }catch(\Exception $e){
//         $response = array(
//            'status' => false,
//            'message' => $e->getMessage(),
//       );
//         return Response::json($response);
//    }
// }

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

                  $person = [];

                  $persondata = [];

                  $index1 = 0;

                  $suhu = [];

                  for ($i=0; $i < count($rows); $i++) {
                    if ($rows[$i][1] == 'Face Authentication Passed') {
                         if ($rows[$i][4] != '-') {
                              $temps = explode('°', $rows[$i][4]);

                              if (str_contains($rows[$i][2], "''")) {
                                   $empname = str_replace("''","'",$rows[$i][2]);
                              }else{
                                   $empname = $rows[$i][2];
                              }


                              $empys = DB::SELECT('select * from employees join employee_syncs on employee_syncs.employee_id = employees.employee_id JOIN sunfish_shift_syncs ON employee_syncs.employee_id = sunfish_shift_syncs.employee_id 
                                        AND shift_date = "'.date('Y-m-d', strtotime($rows[$i][6])).'" where employees.name = "'.$empname.'"');

                              if (count($empys) > 0) {
                                   foreach ($empys as $key) {
                                        $employee_id = $key->employee_id;
                                        $name = $key->name;
                                        $shiftdaily_code = $key->shiftdaily_code;
                                        if ($key->department == null) {
                                             $department = '';
                                        }else{
                                             $department = $key->department;
                                        }
                                        if ($key->section == null) {
                                             $section = '';
                                        }else{
                                             $section = $key->section;
                                        }
                                        if ($key->group == null) {
                                             $group = '';
                                        }else{
                                             $group = $key->group;
                                        }
                                   }

                                   // $ivms = IvmsTemperatureTemp::create([
                                   //      'employee_id' => $employee_id,
                                   //      'name' => $name,
                                   //      'department' => $department,
                                   //      'section' => $section,
                                   //      'group' => $group,
                                   //      'date' => date('Y-m-d', strtotime($rows[$i][6])),
                                   //      'date_in' => $rows[$i][6],
                                   //      'point' => $rows[$i][9],
                                   //      'temperature' => $temps[0],
                                   //      'abnormal_status' => $rows[$i][5],
                                   //      'created_by' => $id_user,
                                   // ]);

                                   $ivms = IvmsTemperature::create([
                                        'employee_id' => $employee_id,
                                        'name' => $name,
                                        'date' => date('Y-m-d', strtotime($rows[$i][6])),
                                        'date_in' => $rows[$i][6],
                                        'point' => $rows[$i][9],
                                        'temperature' => $temps[0],
                                        'shift' => $shiftdaily_code,
                                        'abnormal_status' => $rows[$i][5],
                                        'created_by' => $id_user,
                                   ]);
                                   if ($temps[0] >= '37.5') {
                                        $suhutinggi = array(
                                             'employee_id' => $employee_id,
                                             'name' => $name,
                                             'date' => date('Y-m-d', strtotime($rows[$i][6])),
                                             'date_in' => $rows[$i][6],
                                             'point' => $rows[$i][9],
                                             'temperature' => $temps[0],
                                             'department' => $department,
                                             'section' => $section,
                                             'group' => $group,
                                        );
                                        array_push($suhu,$suhutinggi);
                                   }
                              }else{
                                   $empys = DB::SELECT('select * from employees join employee_syncs on employee_syncs.employee_id = employees.employee_id JOIN sunfish_shift_syncs ON employee_syncs.employee_id = sunfish_shift_syncs.employee_id 
                                        AND shift_date = "'.date('Y-m-d', strtotime($rows[$i][6])).'" where employees.name like "'.$empname.'%"');

                                   foreach ($empys as $key) {
                                        $employee_id = $key->employee_id;
                                        $name = $key->name;
                                        $shiftdaily_code = $key->shiftdaily_code;
                                        $department = $key->department;
                                        $section = $key->section;
                                        $group = $key->group;
                                   }

                                   // $ivms = IvmsTemperatureTemp::create([
                                   //      'employee_id' => $employee_id,
                                   //      'name' => $name,
                                   //      'department' => $department,
                                   //      'section' => $section,
                                   //      'group' => $group,
                                   //      'date' => date('Y-m-d', strtotime($rows[$i][6])),
                                   //      'date_in' => $rows[$i][6],
                                   //      'point' => $rows[$i][9],
                                   //      'temperature' => $temps[0],
                                   //      'abnormal_status' => $rows[$i][5],
                                   //      'created_by' => $id_user,
                                   // ]);
                                   $ivms = IvmsTemperature::create([
                                        'employee_id' => $employee_id,
                                        'name' => $name,
                                        'date' => date('Y-m-d', strtotime($rows[$i][6])),
                                        'date_in' => $rows[$i][6],
                                        'point' => $rows[$i][9],
                                        'temperature' => $temps[0],
                                        'shift' => $shiftdaily_code,
                                        'abnormal_status' => $rows[$i][5],
                                        'created_by' => $id_user,
                                   ]);
                                   if ($temps[0] >= '37.5') {
                                        $suhutinggi = array(
                                             'employee_id' => $employee_id,
                                             'name' => $name,
                                             'date' => date('Y-m-d', strtotime($rows[$i][6])),
                                             'date_in' => $rows[$i][6],
                                             'point' => $rows[$i][9],
                                             'temperature' => $temps[0],
                                             'department' => $department,
                                             'section' => $section,
                                             'group' => $group,
                                        );
                                        array_push($suhu,$suhutinggi);
                                   }
                              }
                         }
                    }
               }

               // $IvmsTemperature = DB::SELECT("SELECT a.employee_id, name, 
               //      -- ( SELECT MAX( temperature ) FROM ivms_temperature_temps WHERE employee_id = a.employee_id ) AS temperature,
               //      -- ( SELECT MIN( date_in ) FROM ivms_temperature_temps WHERE employee_id = a.employee_id ) AS date_in,
               //      temperature,
               //      date_in,
               //      point,
               //      abnormal_status ,
               //      department ,
               //      section ,
               //      `group` ,
               //      date
               //      FROM
               //      `ivms_temperature_temps` AS a");

               // foreach ($IvmsTemperature as $key) {
                    // $ivmscheck = IvmsTemperature::where('employee_id',$key->employee_id)->where('date',$key->date)->first();
                    // $ivms = IvmsTemperature::firstOrNew(['employee_id' => $key->employee_id, 'date' => $key->date]);
                    // $ivms->employee_id = $key->employee_id;
                    // $ivms->name = $key->name;
                    // $ivms->date = $key->date;
                    // $ivms->date_in = $key->date_in;
                    // $ivms->point = $key->point;
                    // $ivms->temperature = $key->temperature;
                    // $ivms->abnormal_status = $key->abnormal_status;
                    // $ivms->created_by = $id_user;
                    // $ivms->save();

                    // if (count($ivmscheck) == 0) {
                         
                    // }
               // }

               // IvmsTemperatureTemp::truncate();
               $miraimobile =DB::SELECT("SELECT
                    *,
                    miraimobile.quiz_logs.created_at AS date_in 
               FROM
                    employees
                    JOIN miraimobile.quiz_logs ON employees.employee_id = miraimobile.quiz_logs.employee_id
                    JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id 
               WHERE
                    employees.end_date IS NULL 
                    AND miraimobile.quiz_logs.answer_date = '".date('Y-m-d')."' 
                    AND miraimobile.quiz_logs.question = 'Suhu Tubuh'");
               foreach ($miraimobile as $val) {
                    $ivmscheck = IvmsTemperature::where('employee_id',$val->employee_id)->where('date',$val->answer_date)->first();

                    $ivms = IvmsTemperature::firstOrNew(['employee_id' => $val->employee_id, 'date' => $val->answer_date]);
                    $ivms->employee_id = $val->employee_id;
                    $ivms->name = $val->name;
                    $ivms->date = $val->answer_date;
                    $ivms->date_in = $val->date_in;
                    $ivms->point = "Mirai Mobile";
                    $tempmobile = floatval($val->answer);
                    $ivms->temperature = $tempmobile;
                    if ($tempmobile >= '37.5') {
                         $ivms->abnormal_status = "Yes";
                    }else{
                         $ivms->abnormal_status = "No";
                    }
                    $ivms->created_by = $id_user;
                    $ivms->save();

                    // if (count($ivmscheck) == 0) {
                    //      if ($tempmobile >= '37.5') {
                    //           $suhutinggi = array(
                    //                'employee_id' => $val->employee_id,
                    //                'name' => $val->name,
                    //                'date' => $val->answer_date,
                    //                'date_in' => $val->date_in,
                    //                'point' => "Mirai Mobile",
                    //                'department' => $val->department,
                    //                'section' => $val->section,
                    //                'group' => $val->group,
                    //                'temperature' => $tempmobile,
                    //           );
                    //           array_push($suhu,$suhutinggi);
                    //      }
                    // }
               }

               $contactList = [];
               $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';

               $contactList2 = [];
               $contactList2[0] = 'prawoto@music.yamaha.com';
               $contactList2[1] = 'putri.sukma.riyanti@music.yamaha.com';
               $contactList2[2] = 'mahendra.putra@music.yamaha.com';

               $mail_to = [];

               for ($i = 0;$i < count($suhu); $i++) {
                    $fc = DB::SELECT("SELECT
                         * 
                    FROM
                         send_emails 
                    WHERE
                         remark = '".$suhu[$i]['section']."'");


                    if (count($fc) > 0) {
                         foreach ($fc as $val) {
                              array_push($mail_to, $val->email);
                         }
                    }
               }

               if (count($suhu) > 0) {
                    Mail::to($mail_to)->cc($contactList2,'CC')->bcc($contactList,'BCC')->send(new SendEmail($suhu, 'temperature'));
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

public function indexMinMoeMonitoring($dept)
{

     $title = "Resume Pengecekan Suhu Tubuh Karyawan";
     $title_jp = "従業員の検温のまとめ";

     if ($dept == 'office') {
          $loc = 'OFC';
     }else if ($dept == 'all') {
          $loc = 'ALL';
     }else{
          $loc = $dept;
     }

     if ($loc == 'OFC') {
          $group = db::select("select DISTINCT(`group`) as grp from employee_syncs where department = '".$loc."' and `group` is not null");
     }else if($loc == 'ALL'){
          $group = db::select("select DISTINCT(`group`) as grp from employee_syncs where `group` is not null");
     }else{
          $group = db::select("select DISTINCT(`group`) as grp from employee_syncs where department = '".$loc."' and `group` is not null");
     }
     return view('temperature.minmoe_monitoring', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'group' => $group,
          'loc' => $loc,
     ))->with('page', 'Temperature');
}

// public function fetchMinMoeMonitoring(Request $request)
// {
//  try {
//      $date_from = $request->get('tanggal_from');
//      $now  = date('Y-m-d');

//      if ($date_from != null) {
//           $now  = $date_from;
//      }

//      $group = '';
//       if(count($request->get('group')) > 0){
//         for ($i=0; $i < count($request->get('group')); $i++) {
//           $group = $group."'".$request->get('group')[$i]."'";
//           if($i != (count($request->get('group'))-1)){
//             $group = $group.',';
//           }
//         }
//         $groupin = " and `group` in (".$group.") ";
//       }
//       else{
//         $groupin = "";
//       }

//      if ($request->get('location') == 'OFC') {
//           $datatoday = DB::SELECT("
//           SELECT
//                count( temperature ) AS count,
//                temperature 
//           FROM
//                `ivms_temperatures`
//                LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id
//                LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//           WHERE
//                ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark = 'OFC' ".$groupin.") 
//                OR ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark = 'Jps' ".$groupin.")
//           GROUP BY
//                temperature ASC");
//      }else if($request->get('location') == 'ALL'){
//           $datatoday = DB::SELECT("
//           SELECT
//                count( temperature ) AS count,
//                temperature 
//           FROM
//                `ivms_temperatures`
//                LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id
//                LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//           WHERE
//                DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL ".$groupin."
//           GROUP BY
//                temperature ASC");
//      }else{
//           $datatoday = DB::SELECT("
//           SELECT
//                count( temperature ) AS count,
//                temperature 
//           FROM
//                `ivms_temperatures`
//                LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id
//                LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//           WHERE
//                 DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark != 'OFC' AND employees.remark != 'Jps' and employee_syncs.department = '".$request->get('location')."' ".$groupin."
//           GROUP BY
//                temperature ASC");
//      }

//           $attendance = [];

//           if ($request->get('location') == 'OFC') {
//                $datacheck = DB::SELECT("SELECT
//                     a.employee_id,
//                     employee_syncs.department,
//                     departments.department_shortname,
//                     COALESCE(employee_syncs.section,'') as section,
//                     a.name,(
//                     SELECT DISTINCT
//                          (
//                          IF
//                               (
//                                    ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
//                                    SPLIT_STRING ( person_name, ' ', 1 ),
//                               IF
//                                    (
//                                         LENGTH( attend_id ) = 6,
//                                         CONCAT( 'PI0', attend_id ),
//                                    IF
//                                         (
//                                              LENGTH( attend_id ) = 5,
//                                              CONCAT( 'PI00', attend_id ),
//                                         IF
//                                              (
//                                                   LENGTH( attend_id ) = 4,
//                                                   CONCAT( 'PI000', attend_id ),
//                                              IF
//                                                   (
//                                                        LENGTH( attend_id ) = 3,
//                                                        CONCAT( 'PI0000', attend_id ),
//                                                   IF
//                                                        (
//                                                             LENGTH( attend_id ) = 2,
//                                                             CONCAT( 'PI00000', attend_id ),
//                                                        IF
//                                                             (
//                                                                  LENGTH( attend_id ) = 1,
//                                                                  CONCAT( 'PI000000', attend_id ),
//                                                             CONCAT( 'PI', attend_id ))))))))) 
//                     FROM
//                          ivms.ivms_attendance 
//                     WHERE
//                          ivms.ivms_attendance.auth_date = '".$now."' 
//                     AND
//                     IF
//                          (
//                               ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
//                               SPLIT_STRING ( person_name, ' ', 1 ),
//                          IF
//                               (
//                                    LENGTH( attend_id ) = 6,
//                                    CONCAT( 'PI0', attend_id ),
//                               IF
//                                    (
//                                         LENGTH( attend_id ) = 5,
//                                         CONCAT( 'PI00', attend_id ),
//                                    IF
//                                         (
//                                              LENGTH( attend_id ) = 4,
//                                              CONCAT( 'PI000', attend_id ),
//                                         IF
//                                              (
//                                                   LENGTH( attend_id ) = 3,
//                                                   CONCAT( 'PI0000', attend_id ),
//                                              IF
//                                                   (
//                                                        LENGTH( attend_id ) = 2,
//                                                        CONCAT( 'PI00000', attend_id ),
//                                                   IF
//                                                        (
//                                                             LENGTH( attend_id ) = 1,
//                                                             CONCAT( 'PI000000', attend_id ),
//                                                        CONCAT( 'PI', attend_id )))))))) = a.employee_id 
//                     ) AS checks,
//                     (select MAX(temperature) from ivms_temperatures where employee_id = a.employee_id and ivms_temperatures.date = '".$now."') as temperature
//                FROM
//                     employees a
//                     JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id 
//                     JOIN departments ON employee_syncs.department = departments.department_name 
//                WHERE
//                     ( a.remark = 'OFC' AND a.end_date IS NULL ".$groupin.") 
//                     OR (
//                          a.remark = 'Jps' 
//                     AND a.end_date IS NULL ".$groupin."
//                     )");
//           }else if($request->get('location') == 'ALL'){
//                $datacheck = DB::SELECT("SELECT
//                     a.employee_id,
//                     employee_syncs.department,
//                     departments.department_shortname,
//                     COALESCE(employee_syncs.section,'') as section,
//                     a.name,(
//                     SELECT DISTINCT
//                          (
//                          IF
//                               (
//                                    ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
//                                    SPLIT_STRING ( person_name, ' ', 1 ),
//                               IF
//                                    (
//                                         LENGTH( attend_id ) = 6,
//                                         CONCAT( 'PI0', attend_id ),
//                                    IF
//                                         (
//                                              LENGTH( attend_id ) = 5,
//                                              CONCAT( 'PI00', attend_id ),
//                                         IF
//                                              (
//                                                   LENGTH( attend_id ) = 4,
//                                                   CONCAT( 'PI000', attend_id ),
//                                              IF
//                                                   (
//                                                        LENGTH( attend_id ) = 3,
//                                                        CONCAT( 'PI0000', attend_id ),
//                                                   IF
//                                                        (
//                                                             LENGTH( attend_id ) = 2,
//                                                             CONCAT( 'PI00000', attend_id ),
//                                                        IF
//                                                             (
//                                                                  LENGTH( attend_id ) = 1,
//                                                                  CONCAT( 'PI000000', attend_id ),
//                                                             CONCAT( 'PI', attend_id ))))))))) 
//                     FROM
//                          ivms.ivms_attendance 
//                     WHERE
//                          ivms.ivms_attendance.auth_date = '".$now."' 
//                     AND
//                     IF
//                          (
//                               ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
//                               SPLIT_STRING ( person_name, ' ', 1 ),
//                          IF
//                               (
//                                    LENGTH( attend_id ) = 6,
//                                    CONCAT( 'PI0', attend_id ),
//                               IF
//                                    (
//                                         LENGTH( attend_id ) = 5,
//                                         CONCAT( 'PI00', attend_id ),
//                                    IF
//                                         (
//                                              LENGTH( attend_id ) = 4,
//                                              CONCAT( 'PI000', attend_id ),
//                                         IF
//                                              (
//                                                   LENGTH( attend_id ) = 3,
//                                                   CONCAT( 'PI0000', attend_id ),
//                                              IF
//                                                   (
//                                                        LENGTH( attend_id ) = 2,
//                                                        CONCAT( 'PI00000', attend_id ),
//                                                   IF
//                                                        (
//                                                             LENGTH( attend_id ) = 1,
//                                                             CONCAT( 'PI000000', attend_id ),
//                                                        CONCAT( 'PI', attend_id )))))))) = a.employee_id 
//                     ) AS checks,
//                     (select MAX(temperature) from ivms_temperatures where employee_id = a.employee_id and ivms_temperatures.date = '".$now."') as temperature
//                FROM
//                     employees a
//                     JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id 
//                     JOIN departments ON employee_syncs.department = departments.department_name 
//                WHERE
//                     a.end_date IS NULL ".$groupin);
//           }else{
//                $datacheck = DB::SELECT("SELECT
//                     a.employee_id,
//                     employee_syncs.department,
//                     departments.department_shortname,
//                     COALESCE(employee_syncs.section,'') as section,
//                     a.name,(
//                     SELECT DISTINCT
//                          (
//                          IF
//                               (
//                                    ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
//                                    SPLIT_STRING ( person_name, ' ', 1 ),
//                               IF
//                                    (
//                                         LENGTH( attend_id ) = 6,
//                                         CONCAT( 'PI0', attend_id ),
//                                    IF
//                                         (
//                                              LENGTH( attend_id ) = 5,
//                                              CONCAT( 'PI00', attend_id ),
//                                         IF
//                                              (
//                                                   LENGTH( attend_id ) = 4,
//                                                   CONCAT( 'PI000', attend_id ),
//                                              IF
//                                                   (
//                                                        LENGTH( attend_id ) = 3,
//                                                        CONCAT( 'PI0000', attend_id ),
//                                                   IF
//                                                        (
//                                                             LENGTH( attend_id ) = 2,
//                                                             CONCAT( 'PI00000', attend_id ),
//                                                        IF
//                                                             (
//                                                                  LENGTH( attend_id ) = 1,
//                                                                  CONCAT( 'PI000000', attend_id ),
//                                                             CONCAT( 'PI', attend_id ))))))))) 
//                     FROM
//                          ivms.ivms_attendance 
//                     WHERE
//                          ivms.ivms_attendance.auth_date = '".$now."' 
//                     AND
//                     IF
//                          (
//                               ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
//                               SPLIT_STRING ( person_name, ' ', 1 ),
//                          IF
//                               (
//                                    LENGTH( attend_id ) = 6,
//                                    CONCAT( 'PI0', attend_id ),
//                               IF
//                                    (
//                                         LENGTH( attend_id ) = 5,
//                                         CONCAT( 'PI00', attend_id ),
//                                    IF
//                                         (
//                                              LENGTH( attend_id ) = 4,
//                                              CONCAT( 'PI000', attend_id ),
//                                         IF
//                                              (
//                                                   LENGTH( attend_id ) = 3,
//                                                   CONCAT( 'PI0000', attend_id ),
//                                              IF
//                                                   (
//                                                        LENGTH( attend_id ) = 2,
//                                                        CONCAT( 'PI00000', attend_id ),
//                                                   IF
//                                                        (
//                                                             LENGTH( attend_id ) = 1,
//                                                             CONCAT( 'PI000000', attend_id ),
//                                                        CONCAT( 'PI', attend_id )))))))) = a.employee_id 
//                     ) AS checks,
//                     (select MAX(temperature) from ivms_temperatures where employee_id = a.employee_id and ivms_temperatures.date = '".$now."') as temperature
//                FROM
//                     employees a
//                     JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id 
//                     JOIN departments ON employee_syncs.department = departments.department_name 
//                WHERE
//                     a.remark != 'OFC' and a.remark != 'Jps' and employee_syncs.department = '".$request->get('location')."' AND a.end_date IS NULL ".$groupin);
//           }
          
//           foreach ($datacheck as $key) {
//                if ($key->checks == null) {
//                     $attendances = DB::connection('sunfish')->select("SELECT
//                          IIF (
//                          Attend_Code LIKE '%ABS%',
//                          'ABS',
//                          IIF (
//                          Attend_Code LIKE '%CK%' 
//                          OR Attend_Code LIKE '%CUTI%' 
//                          OR Attend_Code LIKE '%UPL%',
//                          'Cuti',
//                          IIF (
//                          Attend_Code LIKE '%Izin%',
//                          'Izin',
//                          IIF (
//                          Attend_Code LIKE '%SAKIT%',
//                          'Sakit',
//                          IIF ( Attend_Code LIKE '%LTI%' OR Attend_Code LIKE '%TELAT%', 'Terlambat', IIF ( Attend_Code LIKE '%LTI%', 'Pulang Cepat',
//                          IIF ( Attend_Code LIKE '%PRS%', 'Present', shiftdaily_code ) ) )
//                          ) 
//                          ) 
//                          ) 
//                          ) as attend_code,
//                          shiftdaily_code,
//                          emp_no 
//                          FROM
//                          VIEW_YMPI_Emp_Attendance 
//                          WHERE
//                          (Emp_no = '".$key->employee_id."'
//                          AND FORMAT ( shiftstarttime, 'yyyy-MM-dd' ) = '".$now."')");

//                     if (count($attendances) == 0) {
//                          $miraimobile = DB::SELECT("SELECT * FROM miraimobile.quiz_logs where miraimobile.quiz_logs.answer_date = '".$now."' and miraimobile.quiz_logs.employee_id = '".$key->employee_id."'");

//                          $shiftcode = DB::connection('sunfish')->select("SELECT
//                                    shiftdaily_code,
//                                    emp_no 
//                               FROM
//                                    VIEW_YMPI_Emp_Attendance 
//                               WHERE
//                                    ( Emp_no = '".$key->employee_id."' AND FORMAT ( shiftstarttime, 'yyyy-MM-dd' ) = '".$now."' )");
//                          if (count($shiftcode) > 0) {
//                               foreach ($shiftcode as $val) {
//                                    $shiftdaily_code = $val->shiftdaily_code;
//                               }
//                          }else{
//                               $shiftdaily_code = '-';
//                          }
//                          if (count($miraimobile) > 0) {
//                               $attendances = (object) array(
//                                    '0' => (object) array(
//                                         'attend_code' => 'SBH',
//                                         'shiftdaily_code' => $shiftdaily_code,
//                                         'emp_no' => $key->employee_id
//                                     ),
//                               );
//                          }
//                          $attendance[] = $attendances;
//                     }else{
//                          foreach ($attendances as $val) {
//                               if ($val->attend_code == 'ABS') {
//                                    $miraimobile = DB::SELECT("SELECT * FROM miraimobile.quiz_logs where miraimobile.quiz_logs.answer_date = '".$now."' and miraimobile.quiz_logs.employee_id = '".$key->employee_id."'");
//                                    $shiftcode = DB::connection('sunfish')->select("SELECT
//                                              shiftdaily_code,
//                                              emp_no 
//                                         FROM
//                                              VIEW_YMPI_Emp_Attendance 
//                                         WHERE
//                                              ( Emp_no = '".$key->employee_id."' AND FORMAT ( shiftstarttime, 'yyyy-MM-dd' ) = '".$now."' )");
//                                    if (count($shiftcode) > 0) {
//                                         foreach ($shiftcode as $val) {
//                                              $shiftdaily_code = $val->shiftdaily_code;
//                                         }
//                                    }else{
//                                         $shiftdaily_code = '-';
//                                    }
//                                    if (count($miraimobile) > 0) {
//                                         $attendances = (object) array(
//                                              '0' => (object) array(
//                                                        'attend_code' => 'SBH',
//                                                        'shiftdaily_code' => $shiftdaily_code,
//                                                        'emp_no' => $key->employee_id
//                                                    ),
//                                         );
//                                    }
//                                    $attendance[] = $attendances;
//                               }else{
//                                    $attendance[] = $attendances;
//                               }
//                          }
//                     }
//                }
//           }

//           $dateTitle = date("d M Y", strtotime($now));

//          if ($request->get('location') == 'OFC') {
//                $dataAbnormal = DB::SELECT("SELECT
//                     employee_syncs.employee_id,
//                     employee_syncs.name,
//                     ivms_temperatures.temperature 
//                FROM
//                     ivms_temperatures
//                     LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id 
//                     LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//                WHERE
//                     (employees.remark = 'OFC'  and ivms_temperatures.date = '".$now."' 
//                     AND ivms_temperatures.temperature >= 37.5)
//                      OR
//                      (employees.remark = 'Jps' and ivms_temperatures.date = '".$now."' 
//                     AND ivms_temperatures.temperature >= 37.5) ".$groupin);
//          }else if($request->get('location') == 'ALL'){
//                $dataAbnormal = DB::SELECT("SELECT
//                     employee_syncs.employee_id,
//                     employee_syncs.name,
//                     ivms_temperatures.temperature 
//                FROM
//                     ivms_temperatures
//                     LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id 
//                     LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//                WHERE
//                     ivms_temperatures.date = '".$now."' 
//                     AND ivms_temperatures.temperature >= 37.5 ".$groupin);
//          }else{
//            $dataAbnormal = DB::SELECT("SELECT
//                employee_syncs.employee_id,
//                employee_syncs.name,
//                ivms_temperatures.temperature 
//           FROM
//                ivms_temperatures
//                LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id 
//                LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//           WHERE
//                (employees.remark != 'OFC' and employee_syncs.department = '".$request->get('location')."' and ivms_temperatures.date = '".$now."' 
//                AND ivms_temperatures.temperature >= 37.5)
//                 OR
//                 (employees.remark != 'Jps' and employee_syncs.department = '".$request->get('location')."' and ivms_temperatures.date = '".$now."' 
//                AND ivms_temperatures.temperature >= 37.5) ".$groupin);
//          }

//      $response = array(
//           'status' => true,
//           'message' => 'Get Data Success',
//           'datatoday' => $datatoday,
//           'dateTitle' => $dateTitle,
//           'datacheck' => $datacheck,
//           'dataAbnormal' => $dataAbnormal,
//           'attendance' => $attendance,
//      );

//      return Response::json($response);
// } catch (\Exception $e) {
//      $response = array(
//           'status' => false,
//           'message' => $e->getMessage()
//      );

//      return Response::json($response);
// }
// }

// public function fetchMinMoeMonitoring(Request $request)
// {
//  try {
//      $date_from = $request->get('tanggal_from');
//      $now  = date('Y-m-d');

//      if ($date_from != null) {
//           $now  = $date_from;
//      }
//      $yesterday = date('Y-m-d', strtotime('-1 days', strtotime($now)));

//      $group = '';
//       if(count($request->get('group')) > 0){
//         for ($i=0; $i < count($request->get('group')); $i++) {
//           $group = $group."'".$request->get('group')[$i]."'";
//           if($i != (count($request->get('group'))-1)){
//             $group = $group.',';
//           }
//         }
//         $groupin = " and `group` in (".$group.") ";
//       }
//       else{
//         $groupin = "";
//       }

//      if ($request->get('location') == 'OFC') {
//           $datatoday = DB::SELECT("
//           SELECT
//                count( temperature ) AS count,
//                temperature 
//           FROM
//                `ivms_temperatures`
//                LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id
//                LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//           WHERE
//                ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark = 'OFC' ".$groupin.") 
//                OR ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark = 'Jps' ".$groupin.")
//           GROUP BY
//                temperature ASC");
//      }else if($request->get('location') == 'ALL'){
//           $datatoday = DB::SELECT("
//           SELECT
//                count( temperature ) AS count,
//                temperature 
//           FROM
//                `ivms_temperatures`
//                LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id
//                LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//           WHERE
//                DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL ".$groupin."
//           GROUP BY
//                temperature ASC");
//      }else{
//           $datatoday = DB::SELECT("
//           SELECT
//                count( temperature ) AS count,
//                temperature 
//           FROM
//                `ivms_temperatures`
//                LEFT JOIN employee_syncs ON employee_syncs.employee_id = ivms_temperatures.employee_id
//                LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
//           WHERE
//                 (DATE( date_in ) = '".$now."' 
//                AND employee_syncs.end_date IS NULL 
//                AND employees.remark != 'OFC' 
//                AND employees.remark != 'Jps' 
//                AND employee_syncs.department = '".$request->get('location')."' ".$groupin.")
//                OR
//                (DATE( date_in ) = '".$now."' 
//                AND employee_syncs.end_date IS NULL 
//                AND employees.remark is null
//                AND employee_syncs.department = '".$request->get('location')."' ".$groupin.")
//           GROUP BY
//                temperature ASC");
//      }

//           $attendance = [];

//           if ($request->get('location') == 'OFC') {
//                $datacheck = DB::SELECT("SELECT
//                     a.employee_id,
//                     a.name,
//                     COALESCE ( employee_syncs.department, '' ) AS department,
//                     COALESCE ( employee_syncs.grade_code, '' ) AS grade,
//                     a.remark,
//                     COALESCE (( SELECT department_shortname FROM departments WHERE department_name = employee_syncs.department ), '' ) AS department_shortname,
//                     COALESCE ( employee_syncs.section, '' ) AS section,
//                     (
//                     IF
//                          (
//                               ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_3%',
//                               (
//                               SELECT DISTINCT
//                                    ( ivms.ivms_attendance_triggers.employee_id ) 
//                               FROM
//                                    ivms.ivms_attendance_triggers 
//                               WHERE
//                                    ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                    AND auth_date = DATE( '".$now."' ) - INTERVAL 1 DAY 
//                               ),
//                          IF
//                               (
//                                    ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_2%',(
//                                    SELECT DISTINCT
//                                         ( ivms.ivms_attendance_triggers.employee_id ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                         AND auth_datetime BETWEEN '".$now." 14:30:00' 
//                                         AND '".$now." 17:00:00' 
//                                         ),(
//                                    SELECT DISTINCT
//                                         ( ivms.ivms_attendance_triggers.employee_id ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                    ) 
//                               ) 
//                          )) AS checks,
//                     COALESCE (
//                     IF
//                          (
//                               ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_3%',
//                               (
//                               SELECT
//                                    MAX( auth_datetime ) 
//                               FROM
//                                    ivms.ivms_attendance_triggers 
//                               WHERE
//                                    ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                    AND auth_date = DATE( '".$now."' ) - INTERVAL 1 DAY 
//                               ),
//                          IF
//                               (
//                                    ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_2%',(
//                                    SELECT
//                                         min( auth_datetime ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                         AND auth_datetime BETWEEN '".$now." 14:30:00' 
//                                         AND '".$now." 17:00:00' 
//                                         ),(
//                                    SELECT
//                                         min( auth_datetime ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                    ) 
//                               ) 
//                          ),'-') AS time_in,
//                     ivms.sunfish_shift_syncs.shiftdaily_code,
//                     ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE employee_id = a.employee_id AND ivms_temperatures.date = '".$now."' ) AS temperature,
//                IF
//                     (
//                          ivms.sunfish_shift_syncs.attend_code LIKE '%ABS%',
//                     IF
//                          ((
//                               SELECT
//                                    count( answer_date ) 
//                               FROM
//                                    miraimobile.quiz_logs 
//                               WHERE
//                                    miraimobile.quiz_logs.answer_date = '".$now."' 
//                                    AND miraimobile.quiz_logs.employee_id = a.employee_id 
//                                    ) > 0,
//                               'SBH',
//                               ivms.sunfish_shift_syncs.attend_code 
//                          ),
//                     IF
//                          (
//                               ivms.sunfish_shift_syncs.attend_code LIKE '%ABS%',
//                               'ABS',
//                          IF
//                               (
//                                    ivms.sunfish_shift_syncs.attend_code LIKE '%CK%' 
//                                    OR ivms.sunfish_shift_syncs.attend_code LIKE '%CUTI%' 
//                                    OR ivms.sunfish_shift_syncs.attend_code LIKE '%UPL%',
//                                    'Cuti',
//                               IF
//                                    (
//                                         ivms.sunfish_shift_syncs.attend_code LIKE '%Izin%',
//                                         'Izin',
//                                    IF
//                                         (
//                                              ivms.sunfish_shift_syncs.attend_code LIKE '%SAKIT%',
//                                              'Sakit',
//                                         IF
//                                              (
//                                                   ivms.sunfish_shift_syncs.attend_code LIKE '%LTI%' 
//                                                   OR ivms.sunfish_shift_syncs.attend_code LIKE '%TELAT%',
//                                                   'Terlambat',
//                                              IF
//                                                   (
//                                                        ivms.sunfish_shift_syncs.attend_code LIKE '%LTI%',
//                                                        'Pulang Cepat',
//                                                   IF
//                                                        ( ivms.sunfish_shift_syncs.attend_code LIKE '%PRS%', 'Present', ivms.sunfish_shift_syncs.attend_code ) 
//                                                   ) 
//                                              ) 
//                                         ) 
//                                    ) 
//                               ) 
//                          )) AS attend_code 
//                FROM
//                     employees a
//                     JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
//                     JOIN ivms.sunfish_shift_syncs ON ivms.sunfish_shift_syncs.employee_id = employee_syncs.employee_id 
//                WHERE
//                     ( a.remark = 'OFC' AND a.end_date IS NULL AND ivms.sunfish_shift_syncs.shift_date = '".$now."' ".$groupin." ) 
//                     OR (
//                          a.remark = 'Jps' 
//                     AND a.end_date IS NULL 
//                     AND ivms.sunfish_shift_syncs.shift_date = '".$now."' ".$groupin.")");
//           }else if($request->get('location') == 'ALL'){
//                $datacheck = DB::SELECT("SELECT
//                     a.employee_id,
//                     a.name,
//                     COALESCE ( employee_syncs.department, '' ) AS department,
//                     COALESCE ( employee_syncs.grade_code, '' ) AS grade,
//                     a.remark,
//                     COALESCE (( SELECT department_shortname FROM departments WHERE department_name = employee_syncs.department ), '' ) AS department_shortname,
//                     COALESCE ( employee_syncs.section, '' ) AS section,
//                     (
//                     IF
//                          (
//                               ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_3%',
//                               (
//                               SELECT DISTINCT
//                                    ( ivms.ivms_attendance_triggers.employee_id ) 
//                               FROM
//                                    ivms.ivms_attendance_triggers 
//                               WHERE
//                                    ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                    AND auth_date = DATE( '".$now."' ) - INTERVAL 1 DAY 
//                               ),
//                          IF
//                               (
//                                    ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_2%',(
//                                    SELECT DISTINCT
//                                         ( ivms.ivms_attendance_triggers.employee_id ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                         AND auth_datetime BETWEEN '".$now." 14:30:00' 
//                                         AND '".$now." 17:00:00' 
//                                         ),(
//                                    SELECT DISTINCT
//                                         ( ivms.ivms_attendance_triggers.employee_id ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                    ) 
//                               ) 
//                          )) AS checks,
//                     COALESCE (
//                     IF
//                          (
//                               ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_3%',
//                               (
//                               SELECT
//                                    MAX( auth_datetime ) 
//                               FROM
//                                    ivms.ivms_attendance_triggers 
//                               WHERE
//                                    ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                    AND auth_date = DATE( '".$now."' ) - INTERVAL 1 DAY 
//                               ),
//                          IF
//                               (
//                                    ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_2%',(
//                                    SELECT
//                                         min( auth_datetime ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                         AND auth_datetime BETWEEN '".$now." 14:30:00' 
//                                         AND '".$now." 17:00:00' 
//                                         ),(
//                                    SELECT
//                                         min( auth_datetime ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                    ) 
//                               ) 
//                          ),'-') AS time_in,
//                     ivms.sunfish_shift_syncs.shiftdaily_code,
//                     ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE employee_id = a.employee_id AND ivms_temperatures.date = '".$now."' ) AS temperature,
//                IF
//                     (
//                          ivms.sunfish_shift_syncs.attend_code LIKE '%ABS%',
//                     IF
//                          ((
//                               SELECT
//                                    count( answer_date ) 
//                               FROM
//                                    miraimobile.quiz_logs 
//                               WHERE
//                                    miraimobile.quiz_logs.answer_date = '".$now."' 
//                                    AND miraimobile.quiz_logs.employee_id = a.employee_id 
//                                    ) > 0,
//                               'SBH',
//                               ivms.sunfish_shift_syncs.attend_code 
//                          ),
//                     IF
//                          (
//                               ivms.sunfish_shift_syncs.attend_code LIKE '%ABS%',
//                               'ABS',
//                          IF
//                               (
//                                    ivms.sunfish_shift_syncs.attend_code LIKE '%CK%' 
//                                    OR ivms.sunfish_shift_syncs.attend_code LIKE '%CUTI%' 
//                                    OR ivms.sunfish_shift_syncs.attend_code LIKE '%UPL%',
//                                    'Cuti',
//                               IF
//                                    (
//                                         ivms.sunfish_shift_syncs.attend_code LIKE '%Izin%',
//                                         'Izin',
//                                    IF
//                                         (
//                                              ivms.sunfish_shift_syncs.attend_code LIKE '%SAKIT%',
//                                              'Sakit',
//                                         IF
//                                              (
//                                                   ivms.sunfish_shift_syncs.attend_code LIKE '%LTI%' 
//                                                   OR ivms.sunfish_shift_syncs.attend_code LIKE '%TELAT%',
//                                                   'Terlambat',
//                                              IF
//                                                   (
//                                                        ivms.sunfish_shift_syncs.attend_code LIKE '%LTI%',
//                                                        'Pulang Cepat',
//                                                   IF
//                                                        ( ivms.sunfish_shift_syncs.attend_code LIKE '%PRS%', 'Present', ivms.sunfish_shift_syncs.attend_code ) 
//                                                   ) 
//                                              ) 
//                                         ) 
//                                    ) 
//                               ) 
//                          ))  as attend_code
//                FROM
//                     employees a
//                     JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
//                     JOIN ivms.sunfish_shift_syncs ON ivms.sunfish_shift_syncs.employee_id = employee_syncs.employee_id 
//                WHERE
//                     employee_syncs.end_date IS NULL
//                     AND ivms.sunfish_shift_syncs.shift_date = '".$now."'  ".$groupin);
//           }else{
//                $datacheck = DB::SELECT("SELECT
//                     a.employee_id,
//                     a.name,
//                     COALESCE ( employee_syncs.department, '' ) AS department,
//                     COALESCE ( employee_syncs.grade_code, '' ) AS grade,
//                     a.remark,
//                     COALESCE (( SELECT department_shortname FROM departments WHERE department_name = employee_syncs.department ), '' ) AS department_shortname,
//                     COALESCE ( employee_syncs.section, '' ) AS section,
//                     (
//                     IF
//                          (
//                               ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_3%',
//                               (
//                               SELECT DISTINCT
//                                    ( ivms.ivms_attendance_triggers.employee_id ) 
//                               FROM
//                                    ivms.ivms_attendance_triggers 
//                               WHERE
//                                    ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                    AND auth_date = DATE( '".$now."' ) - INTERVAL 1 DAY 
//                               ),
//                          IF
//                               (
//                                    ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_2%',(
//                                    SELECT DISTINCT
//                                         ( ivms.ivms_attendance_triggers.employee_id ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                         AND auth_datetime BETWEEN '".$now." 14:30:00' 
//                                         AND '".$now." 17:00:00' 
//                                         ),(
//                                    SELECT DISTINCT
//                                         ( ivms.ivms_attendance_triggers.employee_id ) 
//                                    FROM
//                                         ivms.ivms_attendance_triggers 
//                                    WHERE
//                                         ivms.ivms_attendance_triggers.employee_id = a.employee_id 
//                                         AND auth_date = '".$now."' 
//                                    ) 
//                               ) 
//                          )) AS checks,
                    // COALESCE (
                    // IF
                    //      (
                    //           ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_3%',
                    //           (
                    //           SELECT
                    //                MAX( auth_datetime ) 
                    //           FROM
                    //                ivms.ivms_attendance_triggers 
                    //           WHERE
                    //                ivms.ivms_attendance_triggers.employee_id = a.employee_id 
                    //                AND auth_date = DATE( '".$now."' ) - INTERVAL 1 DAY 
                    //           ),
                    //      IF
                    //           (
                    //                ivms.sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_2%',(
                    //                SELECT
                    //                     min( auth_datetime ) 
                    //                FROM
                    //                     ivms.ivms_attendance_triggers 
                    //                WHERE
                    //                     ivms.ivms_attendance_triggers.employee_id = a.employee_id 
                    //                     AND auth_date = '".$now."' 
                    //                     AND auth_datetime BETWEEN '".$now." 14:30:00' 
                    //                     AND '".$now." 17:00:00' 
                    //                     ),(
                    //                SELECT
                    //                     min( auth_datetime ) 
                    //                FROM
                    //                     ivms.ivms_attendance_triggers 
                    //                WHERE
                    //                     ivms.ivms_attendance_triggers.employee_id = a.employee_id 
                    //                     AND auth_date = '".$now."' 
                    //                ) 
                    //           ) 
                    //      ),'-') AS time_in,
//                     ivms.sunfish_shift_syncs.shiftdaily_code,
//                     ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE employee_id = a.employee_id AND ivms_temperatures.date = '".$now."' ) AS temperature,
//                IF
//                     (
//                          ivms.sunfish_shift_syncs.attend_code LIKE '%ABS%',
//                     IF
//                          ((
//                               SELECT
//                                    count( answer_date ) 
//                               FROM
//                                    miraimobile.quiz_logs 
//                               WHERE
//                                    miraimobile.quiz_logs.answer_date = '".$now."' 
//                                    AND miraimobile.quiz_logs.employee_id = a.employee_id 
//                                    ) > 0,
//                               'SBH',
//                               ivms.sunfish_shift_syncs.attend_code 
//                          ),
//                     IF
//                          (
//                               ivms.sunfish_shift_syncs.attend_code LIKE '%ABS%',
//                               'ABS',
//                          IF
//                               (
//                                    ivms.sunfish_shift_syncs.attend_code LIKE '%CK%' 
//                                    OR ivms.sunfish_shift_syncs.attend_code LIKE '%CUTI%' 
//                                    OR ivms.sunfish_shift_syncs.attend_code LIKE '%UPL%',
//                                    'Cuti',
//                               IF
//                                    (
//                                         ivms.sunfish_shift_syncs.attend_code LIKE '%Izin%',
//                                         'Izin',
//                                    IF
//                                         (
//                                              ivms.sunfish_shift_syncs.attend_code LIKE '%SAKIT%',
//                                              'Sakit',
//                                         IF
//                                              (
//                                                   ivms.sunfish_shift_syncs.attend_code LIKE '%LTI%' 
//                                                   OR ivms.sunfish_shift_syncs.attend_code LIKE '%TELAT%',
//                                                   'Terlambat',
//                                              IF
//                                                   (
//                                                        ivms.sunfish_shift_syncs.attend_code LIKE '%LTI%',
//                                                        'Pulang Cepat',
//                                                   IF
//                                                        ( ivms.sunfish_shift_syncs.attend_code LIKE '%PRS%', 'Present', ivms.sunfish_shift_syncs.attend_code ) 
//                                                   ) 
//                                              ) 
//                                         ) 
//                                    ) 
//                               ) 
//                          ))  as attend_code
//                FROM
//                     employees a
//                     JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
//                     JOIN ivms.sunfish_shift_syncs ON ivms.sunfish_shift_syncs.employee_id = employee_syncs.employee_id 
//                WHERE
//                     (employee_syncs.end_date IS NULL 
//                     AND a.remark != 'OFC' 
//                     AND a.remark != 'Jps' 
//                     AND employee_syncs.department = '".$request->get('location')."' 
//                     AND ivms.sunfish_shift_syncs.shift_date = '".$now."' ".$groupin.")
//                     or(
//                     employee_syncs.end_date IS NULL 
//                     AND a.remark is null
//                     AND employee_syncs.department = '".$request->get('location')."' 
//                     AND ivms.sunfish_shift_syncs.shift_date = '".$now."' ".$groupin.")");
//           }

//           $dateTitle = date("d M Y", strtotime($now));

//      $response = array(
//           'status' => true,
//           'message' => 'Get Data Success',
//           'datatoday' => $datatoday,
//           'dateTitle' => $dateTitle,
//           'now' => $now,
//           'yesterday' => $yesterday,
//           'datacheck' => $datacheck,
//           'attendance' => $attendance,
//      );

//      return Response::json($response);
// } catch (\Exception $e) {
//      $response = array(
//           'status' => false,
//           'message' => $e->getMessage()
//      );

//      return Response::json($response);
// }
// }

public function fetchMinMoeMonitoring(Request $request)
{
 try {
     $date_from = $request->get('tanggal_from');
     $now  = date('Y-m-d');

     if ($date_from != null) {
          $now  = $date_from;
     }
     $yesterday = date('Y-m-d', strtotime('-1 days', strtotime($now)));

     $group = '';
      if(count($request->get('group')) > 0){
        for ($i=0; $i < count($request->get('group')); $i++) {
          $group = $group."'".$request->get('group')[$i]."'";
          if($i != (count($request->get('group'))-1)){
            $group = $group.',';
          }
        }
        $groupin = " and `group` in (".$group.") ";
      }
      else{
        $groupin = "";
      }

     if ($request->get('location') == 'OFC') {
          $datatoday = DB::SELECT("SELECT
               count( b.employee_id ) AS count,
               b.temperature 
          FROM
               (
               SELECT DISTINCT
                    ( a.employee_id ) AS employee_id,
                    ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature 
               FROM
                    ivms_temperatures AS a
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
                    LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
               WHERE
                    ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark = 'OFC' ".$groupin.") 
                    OR ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark = 'Jps' ".$groupin.") 
               ) b 
          GROUP BY
               b.temperature");
     }else if($request->get('location') == 'ALL'){
          $datatoday = DB::SELECT("
                         SELECT
                    count( b.employee_id ) as count,
                    b.temperature 
               FROM
                    (
                    SELECT DISTINCT
                         ( a.employee_id ) AS employee_id,
                         ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature 
                    FROM
                         ivms_temperatures AS a
                         LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
                         LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
                    WHERE
                         DATE( date_in ) = '".$now."' 
                         AND employee_syncs.end_date IS NULL ".$groupin."
                    ) b 
               GROUP BY
                    b.temperature");
     }else{
          $datatoday = DB::SELECT("
          SELECT
               count( b.employee_id ) AS count,
               b.temperature 
          FROM
               (
               SELECT DISTINCT
                    ( a.employee_id ) AS employee_id,
                    ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature 
               FROM
                    ivms_temperatures AS a
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
                    LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
               WHERE
                    (
                         DATE( date_in ) = '".$now."' 
                         AND employee_syncs.end_date IS NULL 
                         AND employees.remark != 'OFC' 
                         AND employees.remark != 'Jps' 
                         AND employee_syncs.department = '".$request->get('location')."' ".$groupin." 
                    ) 
                    OR ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark IS NULL AND employee_syncs.department = '".$request->get('location')."' ".$groupin." ) 
               ) b 
          GROUP BY
               b.temperature");
     }

          $attendance = [];

          if ($request->get('location') == 'OFC') {
               $datacheck = DB::SELECT("SELECT
                    employees.employee_id,
                    employee_syncs.name,
                    sunfish_shift_syncs.shiftdaily_code,
                    sunfish_shift_syncs.attend_code,
                    COALESCE ( department_shortname, '' ) AS department_shortname,
                    COALESCE ( employee_syncs.section, '' ) AS section,
                    COALESCE ( employee_syncs.`group`, '' ) AS groups,
                    employees.remark
               FROM
                    employees
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id
                    LEFT JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = employee_syncs.employee_id
                    LEFT JOIN departments ON departments.department_name = employee_syncs.department 
               WHERE
                    (employees.remark = 'OFC' and
                    employee_syncs.end_date IS NULL 
                    AND sunfish_shift_syncs.shift_date = '".$now."' ".$groupin.")
                    OR
                    (employees.remark = 'Jps' and
                    employee_syncs.end_date IS NULL 
                    AND sunfish_shift_syncs.shift_date = '".$now."' ".$groupin.")");
          }else if ($request->get('location') == 'ALL') {
               $datacheck = DB::SELECT("SELECT
                    employee_syncs.employee_id,
                    sunfish_shift_syncs.shiftdaily_code,
                    sunfish_shift_syncs.attend_code,
                    COALESCE ( department_shortname, '' ) AS department_shortname,
                    COALESCE ( employee_syncs.section, '' ) AS section,
                    COALESCE ( employee_syncs.`group`, '' ) AS groups,
                    employees.remark,
                    employee_syncs.name
               FROM
                    employees
                    JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id
                    LEFT JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = employee_syncs.employee_id 
                    LEFT JOIN departments ON departments.department_name = employee_syncs.department 
               WHERE
                    employee_syncs.end_date IS NULL 
                    AND sunfish_shift_syncs.shift_date = '".$now."'
                    ".$groupin."");
          }else{
               $datacheck = DB::SELECT("SELECT
                    employees.employee_id,
                    employee_syncs.name,
                    sunfish_shift_syncs.shiftdaily_code,
                    sunfish_shift_syncs.attend_code,
                    COALESCE ( department_shortname, '' ) AS department_shortname,
                    COALESCE ( employee_syncs.section, '' ) AS section,
                    COALESCE ( employee_syncs.`group`, '' ) AS groups,
                    employees.remark
               FROM
                    employees
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id
                    LEFT JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = employee_syncs.employee_id
                    LEFT JOIN departments ON departments.department_name = employee_syncs.department
               WHERE
                    (
                         employees.remark != 'OFC' 
                         AND employees.remark != 'Jps' 
                         AND employee_syncs.end_date IS NULL 
                         AND employee_syncs.department = '".$request->get('location')."' 
                         AND sunfish_shift_syncs.shift_date = '".$now."' ".$groupin."
                    ) 
                    OR (
                         employees.remark IS NULL 
                         AND employee_syncs.end_date IS NULL 
                    AND employee_syncs.department = '".$request->get('location')."' 
                    AND sunfish_shift_syncs.shift_date = '".$now."' ".$groupin.")");
          }

          foreach ($datacheck as $key) {
               $checks = null;
               $time_in = '-';
               $attendances = DB::SELECT("SELECT
               IF
                    (
                         '".$key->shiftdaily_code."' LIKE '%Shift_3%',
                         (
                         SELECT DISTINCT
                              ( ivms.ivms_attendance_triggers.employee_id ) 
                         FROM
                              ivms.ivms_attendance_triggers 
                         WHERE
                              ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                              AND auth_date = DATE( '".$now."' )
                              AND auth_datetime BETWEEN '".$now." 22:00:00' 
                                   AND '".$now." 23:59:59'
                         ),
                    IF
                         (
                              '".$key->shiftdaily_code."' LIKE '%Shift_2%',(
                              SELECT DISTINCT
                                   ( ivms.ivms_attendance_triggers.employee_id ) 
                              FROM
                                   ivms.ivms_attendance_triggers 
                              WHERE
                                   ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                                   AND auth_date = '".$now."' 
                                   AND auth_datetime BETWEEN '".$now." 14:00:00' 
                                   AND '".$now." 17:00:00' 
                                   ),(
                              SELECT DISTINCT
                                   ( ivms.ivms_attendance_triggers.employee_id ) 
                              FROM
                                   ivms.ivms_attendance_triggers 
                              WHERE
                                   ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                                   AND auth_date = '".$now."' 
                              ) 
                         ) 
                    ) AS checks,
                    COALESCE (
                    IF
                         (
                              '".$key->shiftdaily_code."' LIKE '%Shift_3%',
                              (
                              SELECT
                                   MAX( auth_datetime ) 
                              FROM
                                   ivms.ivms_attendance_triggers 
                              WHERE
                                   ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                                   AND auth_date = DATE( '".$now."' )
                                   AND auth_datetime BETWEEN '".$now." 22:00:00' 
                                   AND '".$now." 23:59:59'
                              ),
                         IF
                              (
                                   '".$key->shiftdaily_code."' LIKE '%Shift_2%',(
                                   SELECT
                                        min( auth_datetime ) 
                                   FROM
                                        ivms.ivms_attendance_triggers 
                                   WHERE
                                        (ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                                        AND auth_date = '".$now."' 
                                        AND auth_datetime BETWEEN '".$now." 14:00:00' 
                                        AND '".$now." 17:00:00' )
                                        
                                        ),(
                                   SELECT
                                        min( auth_datetime ) 
                                   FROM
                                        ivms.ivms_attendance_triggers 
                                   WHERE
                                        ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                                        AND auth_date = '".$now."' 
                                   ) 
                              ) 
                         ),'-') AS time_in,
                         IF(COALESCE (
                    IF
                         (
                              '".$key->shiftdaily_code."' LIKE '%Shift_3%',
                              (
                              SELECT
                                   MAX( auth_datetime ) 
                              FROM
                                   ivms.ivms_attendance_triggers 
                              WHERE
                                   ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                                   AND auth_date = DATE( '".$now."' )
                                   AND auth_datetime BETWEEN '".$now." 22:00:00' 
                                   AND '".$now." 23:59:59'
                              ),
                         IF
                              (
                                   '".$key->shiftdaily_code."' LIKE '%Shift_2%',(
                                   SELECT
                                        min( auth_datetime ) 
                                   FROM
                                        ivms.ivms_attendance_triggers 
                                   WHERE
                                        (ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                                        AND auth_date = '".$now."' 
                                        AND auth_datetime BETWEEN '".$now." 14:00:00' 
                                        AND '".$now." 17:00:00' )
                                        
                                        ),(
                                   SELECT
                                        min( auth_datetime ) 
                                   FROM
                                        ivms.ivms_attendance_triggers 
                                   WHERE
                                        ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
                                        AND auth_date = '".$now."' 
                                   ) 
                              ) 
                         ),'-') = '-','-',(select max(temperature) from ivms_temperatures where ivms_temperatures.employee_id = employee_syncs.employee_id and date_in = time_in)) as temperature,
                         ( SELECT DISTINCT ( point ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = employee_syncs.employee_id AND date_in = time_in ) AS point 
               FROM
                    employee_syncs 
               WHERE
                    employee_syncs.end_date IS NULL 
                    AND employee_syncs.employee_id = '".$key->employee_id."'");
// OR(
//                                         ivms.ivms_attendance_triggers.employee_id = employee_syncs.employee_id 
//                                         AND auth_date = '".$now."' 
//                                         AND auth_datetime >= '".$now." 00:00:01' )

               foreach ($attendances as $val) {
                    $checks = $val->checks;
                    $time_in = $val->time_in;
                    $temperature = $val->temperature;
                    $point = $val->point;
               }

               $attendances2 = array(
                              'temperature' => $temperature,
                              'employee_id' => $key->employee_id,
                              'name' => $key->name,
                              'shiftdaily_code' => $key->shiftdaily_code,
                              'attend_code' => $key->attend_code,
                              'department_shortname' => $key->department_shortname,
                              'section' => $key->section,
                              'groups' => $key->groups,
                              'remark' => $key->remark,
                              'checks' => $checks,
                              'time_in' => $time_in,
                              'point' => $point,);
               $attendance[] = $attendances2;
          }

          $dateTitle = date("d M Y", strtotime($now));

          if ($request->get('location') == 'OFC') {
               $dataabnormal = DB::SELECT("SELECT DISTINCT
                    ( a.employee_id ) AS employee_id,
                    employee_syncs.`name`,
                    ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature,
                    (
                    SELECT
                         MIN( date_in ) 
                    FROM
                         ivms_temperatures 
                    WHERE
                         ivms_temperatures.employee_id = a.employee_id 
                         AND DATE( date_in ) = '".$now."' 
                         AND ivms_temperatures.temperature = ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) 
                    ) AS date_in,
                    a.point,
                    COALESCE ( departments.department_shortname, '' ) AS department_shortname,
                    COALESCE ( employee_syncs.section, '' ) AS section,
                    COALESCE ( employee_syncs.`group`, '' ) AS groups,
                    a.shift
               FROM
                    ivms_temperatures AS a
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
                    LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id
                    LEFT JOIN departments ON employee_syncs.department = departments.department_name 
               WHERE
                    ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark = 'OFC' ".$groupin." ) 
                    OR (
                         DATE( date_in ) = '".$now."' 
                         AND employee_syncs.end_date IS NULL 
                    AND employees.remark = 'Jps' ".$groupin."
                    )
               ");
          }else if($request->get('location') == 'ALL'){
               $dataabnormal = DB::SELECT("SELECT DISTINCT
                    ( a.employee_id ) AS employee_id,
                    employee_syncs.`name`,
                    ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature,
                    (
                    SELECT
                         MIN( date_in ) 
                    FROM
                         ivms_temperatures 
                    WHERE
                         ivms_temperatures.employee_id = a.employee_id 
                         AND DATE( date_in ) = '".$now."' 
                         AND ivms_temperatures.temperature = ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) 
                    ) AS date_in,
                    a.point,
                    COALESCE ( departments.department_shortname, '' ) AS department_shortname,
                    COALESCE ( employee_syncs.section, '' ) AS section,
                    COALESCE ( employee_syncs.`group`, '' ) AS groups,
                    a.shift
               FROM
                    ivms_temperatures AS a
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
                    LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id
                    LEFT JOIN departments ON employee_syncs.department = departments.department_name 
               WHERE
                    DATE( a.date_in ) = '".$now."' 
                    AND employee_syncs.end_date IS NULL ".$groupin."
                         ");
          }else{
               $dataabnormal = DB::SELECT("SELECT DISTINCT
                    ( a.employee_id ) AS employee_id,
                    employee_syncs.`name`,
                    ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature,
                    (
                    SELECT
                         MIN( date_in ) 
                    FROM
                         ivms_temperatures 
                    WHERE
                         ivms_temperatures.employee_id = a.employee_id 
                         AND DATE( date_in ) = '".$now."' 
                         AND ivms_temperatures.temperature = ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) 
                    ) AS date_in,
                    a.point,
                    COALESCE ( departments.department_shortname, '' ) AS department_shortname,
                    COALESCE ( employee_syncs.section, '' ) AS section,
                    COALESCE ( employee_syncs.`group`, '' ) AS groups,
                    a.shift
               FROM
                    ivms_temperatures AS a
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
                    LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id
                    LEFT JOIN departments ON employee_syncs.department = departments.department_name 
               WHERE
                    (
                         employees.remark != 'OFC' 
                         AND employees.remark != 'Jps' 
                         AND DATE( date_in ) = '".$now."' 
                         AND employee_syncs.department = '".$request->get('location')."' 
                         AND employee_syncs.end_date IS NULL ".$groupin."
                    ) 
                    OR (
                         employees.remark IS NULL 
                         AND DATE( date_in ) = '".$now."' 
                         AND employee_syncs.department = '".$request->get('location')."' 
                    AND employee_syncs.end_date IS NULL ".$groupin."
                    )
                    ");
          }

     $response = array(
          'status' => true,
          'message' => 'Get Data Success',
          'datatoday' => $datatoday,
          'dataabnormal' => $dataabnormal,
          'dateTitle' => $dateTitle,
          'now' => $now,
          'yesterday' => $yesterday,
          'attendance' => $attendance,
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

public function fetchDetailMinMoeMonitoring(Request $request)
{
 try {
     $date_from = $request->get('tanggal_from');
     $now  = date('Y-m-d');

     if ($date_from != null) {
          $now  = $date_from;
     }

     $temperature = $request->get('temperature');

     $group = '';
      if(count($request->get('group')) > 0){
        for ($i=0; $i < count($request->get('group')); $i++) {
          $group = $group."'".$request->get('group')[$i]."'";
          if($i != (count($request->get('group'))-1)){
            $group = $group.',';
          }
        }
        $groupin = " and `group` in (".$group.") ";
      }
      else{
        $groupin = "";
      }

     if ($request->get('location') == 'OFC') {
          $detail = DB::SELECT("SELECT DISTINCT
               ( a.employee_id ) AS employee_id,
               employee_syncs.`name`,
               ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature,
               (
               SELECT
                    MIN( date_in ) 
               FROM
                    ivms_temperatures 
               WHERE
                    ivms_temperatures.employee_id = a.employee_id 
                    AND DATE( date_in ) = '".$now."' 
                    AND ivms_temperatures.temperature = ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) 
               ) AS date_in,
               a.point,
               COALESCE ( departments.department_shortname, '' ) AS department_shortname,
               COALESCE ( employee_syncs.section, '' ) AS section,
               COALESCE ( employee_syncs.`group`, '' ) AS groups,
               a.shift
          FROM
               ivms_temperatures AS a
               LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
               LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id
               LEFT JOIN departments ON employee_syncs.department = departments.department_name 
          WHERE
               ( DATE( date_in ) = '".$now."' AND employee_syncs.end_date IS NULL AND employees.remark = 'OFC' ".$groupin." ) 
               OR (
                    DATE( date_in ) = '".$now."' 
                    AND employee_syncs.end_date IS NULL 
               AND employees.remark = 'Jps' ".$groupin."
               )
          ");
     }else if($request->get('location') == 'ALL'){
          $detail = DB::SELECT("SELECT DISTINCT
               ( a.employee_id ) AS employee_id,
               employee_syncs.`name`,
               ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature,
               (
               SELECT
                    MIN( date_in ) 
               FROM
                    ivms_temperatures 
               WHERE
                    ivms_temperatures.employee_id = a.employee_id 
                    AND DATE( date_in ) = '".$now."' 
                    AND ivms_temperatures.temperature = ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) 
               ) AS date_in,
               a.point,
               COALESCE ( departments.department_shortname, '' ) AS department_shortname,
               COALESCE ( employee_syncs.section, '' ) AS section,
               COALESCE ( employee_syncs.`group`, '' ) AS groups,
               a.shift
          FROM
               ivms_temperatures AS a
               LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
               LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id
               LEFT JOIN departments ON employee_syncs.department = departments.department_name 
          WHERE
               DATE( a.date_in ) = '".$now."' 
               AND employee_syncs.end_date IS NULL ".$groupin."
                    ");
     }else{
          $detail = DB::SELECT("SELECT DISTINCT
               ( a.employee_id ) AS employee_id,
               employee_syncs.`name`,
               ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) AS temperature,
               (
               SELECT
                    MIN( date_in ) 
               FROM
                    ivms_temperatures 
               WHERE
                    ivms_temperatures.employee_id = a.employee_id 
                    AND DATE( date_in ) = '".$now."' 
                    AND ivms_temperatures.temperature = ( SELECT MAX( temperature ) FROM ivms_temperatures WHERE ivms_temperatures.employee_id = a.employee_id AND DATE( date_in ) = '".$now."' ) 
               ) AS date_in,
               a.point,
               COALESCE ( departments.department_shortname, '' ) AS department_shortname,
               COALESCE ( employee_syncs.section, '' ) AS section,
               COALESCE ( employee_syncs.`group`, '' ) AS groups,
               a.shift
          FROM
               ivms_temperatures AS a
               LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
               LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id
               LEFT JOIN departments ON employee_syncs.department = departments.department_name 
          WHERE
               (
                    employees.remark != 'OFC' 
                    AND employees.remark != 'Jps' 
                    AND DATE( date_in ) = '".$now."' 
                    AND employee_syncs.department = '".$request->get('location')."' 
                    AND employee_syncs.end_date IS NULL ".$groupin."
               ) 
               OR (
                    employees.remark IS NULL 
                    AND DATE( date_in ) = '".$now."' 
                    AND employee_syncs.department = '".$request->get('location')."' 
               AND employee_syncs.end_date IS NULL ".$groupin."
               )
               ");
     }


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
