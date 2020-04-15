<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;

class MiraiMobileController extends Controller
{

  public function index()
  {
    $title = 'MIRAI Mobile Report';
    $title_jp = '??';

    return view('mirai_mobile.index', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'MIRAI Mobile');
  }

  public function health(){
    $title = 'Employee Health Report';
    $title_jp = '従業員の健康報告';

    return view('mirai_mobile.report_health', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'Employee Health Report');
  }

  public function shift(){
    $title = 'Employee Work Grup';
    $title_jp = '';

    return view('mirai_mobile.report_shift', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'Employee Work Grup');
  }

  public function fetch_detail(Request $request){

    $tgl = $request->get("tgl");

    if(strlen($request->get('datefrom')) > 0){
      $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    }

    if(strlen($request->get('dateto')) > 0){
      $dateto = date('Y-m-d', strtotime($request->get('dateto')));
    }

    // $status = $request->get('status');

    // if ($status != null) {

    //   $stat = 'and employee_id = "'.$status.'"';


      // if ($status == "Employee Submit") {
      //   $data = DB::connection('mobile')->select("SELECT DISTINCT(quiz_logs.employee_id),quiz_logs.answer_date,employees.employee_id,employees.name,employees.department,employees.section,employees.group from employees LEFT JOIN quiz_logs on quiz_logs.employee_id = employees.employee_id where quiz_logs.employee_id is not null and employees.end_date is null and answer_date = DATE(NOW()) and employees.keterangan is null");
      // }

      // if ($status == "Employee Not Submit") {
    $data = DB::connection('mobile')->select("select employees.employee_id,employees.name,employees.department,employees.section,employees.group from employees where employees.employee_id not in (select DISTINCT(quiz_logs.employee_id) from quiz_logs LEFT JOIN employees on quiz_logs.employee_id = employees.employee_id where answer_date = '".$tgl."' and employees.end_date is null and employees.keterangan is null) and end_date is null and keterangan is null");
      // }
    // }else{
    //   $stat = '';
    // }

    // return DataTables::of($data)

    // ->editColumn('answer_date', function($detail){
    //   return date('d F Y', strtotime($detail->answer_date));
    // })
    // ->editColumn('status', function($detail){

    // })

    // ->rawColumns(['status' => 'status'])
    // ->make(true);
    $response = array(
      'status' => true,
      'lists' => $data,
    );
    return Response::json($response);
  }

  public function fetchHealthData(Request $request)
  {
    $tanggal = $request->get("tanggal");
    if ($tanggal == null) {
      $tgl = '';
    }
    else{
      $tgl = "where tanggal = '".$tanggal."'";
    }

    // $data = DB::connection('mobile')->select("SELECT
    //   employee_id,
    //   name,
    //   DATE(
    //   NOW()) AS answer_date,
    //   (
    //   SELECT
    //   CONCAT(MIN( created_at ),'/',latitude,'/',longitude) AS masuk 
    //   FROM
    //   quiz_logs 
    //   WHERE
    //   employee_id = employees.employee_id 
    //   AND answer_date = DATE(
    //   NOW())) AS masuk,
    //   (
    //   SELECT
    //   CONCAT(IF
    //   (
    //   TIMESTAMPDIFF(
    //   MINUTE,
    //   MIN( created_at ),
    //   MAX( created_at )) < 60,
    //   NULL,
    //   IF
    //   (
    //   MAX( created_at ) = MIN( created_at ),
    //   NULL,
    //   MAX( created_at ))),'/',latitude,'/',longitude) AS keluar 
    //   FROM
    //   quiz_logs 
    //   WHERE
    //   employee_id = employees.employee_id 
    //   AND answer_date = DATE(
    //   NOW())) AS keluar
    //   FROM
    //   `employees`
    //   ".$tgl."");

    $q =  'select att.*, groups.remark from
    (select employee_id, `name`, answer_date, SUM(masuk) lat_in, SUM(masuk1) lng_in, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SUM(keluar),null) lat_out, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SUM(keluar2),null) lng_out, SEC_TO_TIME(SUM(time_in)) time_in, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SEC_TO_TIME(SUM(time_out)),null) time_out from
    (
    SELECT employee_id, `name`, answer_date, latitude as masuk, longitude as masuk1, 0 as keluar, 0 as keluar2, id as id_in, 0 as id_out, DATE_FORMAT(created_at, "%H") as jam_in, 0 as jam_out, TIME_TO_SEC(DATE_FORMAT(created_at, "%H:%i")) as time_in, 0 as time_out FROM quiz_logs
    WHERE id IN (
    SELECT MIN(id)
    FROM quiz_logs
    GROUP BY employee_id, `name`, answer_date
    )
    union all
    SELECT employee_id, `name`, answer_date, 0 as masuk, 0 as masuk1, latitude as keluar, longitude as keluar2, 0 as id_in, id as id_out, 0 as jam_in,  DATE_FORMAT(created_at, "%H") as jam_out, 0 as time_in,  TIME_TO_SEC(DATE_FORMAT(created_at, "%H:%i")) as time_out FROM quiz_logs
    WHERE id IN (
    SELECT MAX(id)
    FROM quiz_logs
    GROUP BY employee_id, `name`, answer_date
    )
    ) as semua
    group by employee_id, `name`, answer_date) as att
    left join groups on att.employee_id = groups.employee_id AND att.answer_date = groups.tanggal';

    $response = array(
      'status' => true,
      'lists' => DB::connection('mobile')->select($q),
    );
    return Response::json($response);
  }

  public function fetchShiftData(Request $request)
  {
    $q =  'select * from groups';

    $response = array(
      'status' => true,
      'lists' => DB::connection('mobile')->select($q),
    );
    return Response::json($response);
  }

  public function display_health(){
    $title = 'Employee Health Report';
    $title_jp = '作業者不良率';

    return view('mirai_mobile.health_report', array(
     'title' => $title,
     'title_jp' => $title_jp
   ))->with('page', 'Employee Health Report');
  }

  public function fetch_health(Request $request){

    $datefrom = date("Y-m-d", strtotime('-30 days'));
    $dateto = date("Y-m-d");

    if(strlen($request->get('datefrom')) > 0){
      $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    }

    if(strlen($request->get('dateto')) > 0){
      $dateto = date('Y-m-d', strtotime($request->get('dateto')));
    }

      //per tgl
    $data = DB::connection('mobile')->select("
     select distinct answer_date, 
     (select count(employee_id) as emp from employees where end_date is null and keterangan is null) as karyawan,
     (select count(employee_id) as emp from employees where end_date is null and keterangan is null) - emplo.mengisi as belum,
     emplo.mengisi
     from
     (select answer_date, count(employee_id) as mengisi from
     (select answer_date, quiz_logs.employee_id from quiz_logs left join employees on quiz_logs.employee_id = employees.employee_id where keterangan is null
     group by employee_id, answer_date) dd
     group by answer_date) emplo");

    $data_sakit = DB::connection('mobile')->select("
      select cat.answer_date, cat.question, IFNULL(ans,0) as count from
      (select distinct answer_date, question from quiz_logs where question <> 'Suhu Tubuh') cat
      left join
      (select answer_date, question, count(answer) ans from quiz_logs where answer = 'Iya' group by question, answer_date) as tidak
      on cat.question = tidak.question and cat.answer_date = tidak.answer_date");

    $cat_sakit = DB::connection('mobile')->select("
      select distinct answer_date, question from quiz_logs where question <> 'Suhu Tubuh'");

    // $q2 = DB::connection('mobile')->select("SELECT employee_id, `name`, answer_date, latitude as masuk, longitude as masuk1, 0 as keluar, 0 as keluar2 FROM `quiz_logs` group by employee_id, `name`, answer_date");

    $year = date('Y');

    $response = array(
      'status' => true,
      'datas' => $data,
      'sakit' => $data_sakit,
      'cat_sakit' => $cat_sakit
    );

    return Response::json($response);
  }

  public function fetchLocationEmployee(Request $request){

    $loc = $this->getLocation($request->get('lat'), $request->get('lng'));

    $loc1 = json_encode($loc);

    $loc2 = explode('\"',$loc1);

    $keyStateDistrict = array_search('state_district', $loc2);
    $keyVillage = array_search('village', $loc2);
    $keyState = array_search('state', $loc2);
    $keyPostcode = array_search('postcode', $loc2);
    $keyCountry = array_search('country', $loc2);

    $data = array(
      'city' => $loc2[$keyStateDistrict + 2],
      'village' => $loc2[$keyVillage + 2],
      'province' => $loc2[$keyState + 2],
      'postcode' => $loc2[$keyPostcode + 2],
      'country' => $loc2[$keyCountry + 2]
    );

    $response = array(
      'status' => true,
      'data' => $data,
    );
    return Response::json($response);

  }

  public function getLocation($lat, $long){

    $url = "https://locationiq.org/v1/reverse.php?key=29e75d503929a1&lat=".$lat."&lon=".$long."&format=json";
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    $results = curl_exec($curlHandle);
    curl_close($curlHandle);

    $response = array(
      'status' => true,
      'data' => $results,
    );
    return Response::json($response);
  }
}