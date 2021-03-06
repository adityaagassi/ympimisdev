<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Excel;
use App\EmployeeSync;

class MiraiMobileController extends Controller
{

  public function index()
  {
    $title = 'MIRAI Mobile Report';
    $title_jp = 'モバイルMIRAIの記録';

    return view('mirai_mobile.index', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'MIRAI Mobile');
  }

  public function indexCoronaMap(){
    $title = 'YMPI Corona Mapping';
    $title_jp = '??';

    return view('mirai_mobile.corona_map', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'MIRAI Mobile');
  }

  public function indexCoronaInformation(){
    $title = 'Daily Corona Data';
    $title_jp = 'インドネシア国内の新型コロナウイルス感染症の感染拡大データ';

    return view('mirai_mobile.corona_information', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'MIRAI Mobile');
  }

  public function fetchCoronaInformation(Request $request){
    $corona_informations = db::table('corona_informations')->orderBy('date', 'ASC')->get();

    $last_update = db::table('corona_informations')->orderBy('date', 'desc')->first();

    $now = date('Y-m-d', strtotime($last_update->date));
    $yesterday = date('Y-m-d',strtotime("-1 days"));
    if(strlen($request->get('date_now')) > 0 ){
      $now = date('Y-m-d', strtotime($request->get('date_now')));
      $yesterday = date('Y-m-d', strtotime('-1 days', strtotime($request->get('date_now'))));
    }

    $detail_now = db::table('corona_informations')->orderBy('date', 'ASC')->where('date', '=', $now)->get();
    $detail_yesterday = db::table('corona_informations')->orderBy('date', 'ASC')->where('date', '=', $yesterday)->get();

    $response = array(
      'status' => true,
      'corona_informations' => $corona_informations,
      'detail_now' => $detail_now,
      'detail_yesterday' => $detail_yesterday,
      'now' => $now
    );
    return Response::json($response);

  }

  public function health(){
    $title = 'Employee Health Report';
    $title_jp = '従業員の健康報告';


    if(Auth::user()->role_code == 'HR' || Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'S'){
      return view('mirai_mobile.report_health', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Employee Health Report');
    }

    return view('404',  
      array('message' => 'Silahkan menghubungi bagian HR untuk data absensi MIRAI Mobile.'
    )
    );
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
    // $remark = $request->get("remark");

    if(strlen($request->get('datefrom')) > 0){
      $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    }

    if(strlen($request->get('dateto')) > 0){
      $dateto = date('Y-m-d', strtotime($request->get('dateto')));
    }
    
    $data = DB::connection('mobile')->select("SELECT
      employee_id,
      name,
      kode,
      department,
      section,
      groupes,
      COALESCE(created_at,'Tidak Tersedia') as created_at,
      COALESCE(time(created_at),'Tidak Tersedia') as jam,
      remark
      FROM
      (
      SELECT
      groups.employee_id,
      groups.name,
      groups.kode,
      employees.department,
      employees.section,
      employees.group as groupes,
    --    log.department,
    log.created_at,
    IF
    (
    time( log.created_at ) > '07:00:00' 
    AND time( log.created_at ) <= '08:00:00', 'LTI', IF ( time( log.created_at ) > '08:00:00' 
    OR log.created_at IS NULL,
    'ABS',
    IF
    ( time( log.created_at ) <= '07:00:00', 'PRS', 'Unidentified' ))) AS remark 
    FROM
    groups
    LEFT JOIN (
    SELECT
    employee_id,
    name,
    department,
    min( created_at ) AS created_at 
    FROM
    quiz_logs 
    WHERE
    date( created_at ) = '".$tgl."' 
    GROUP BY
    employee_id,
    name,
    department 
    ) AS log ON log.employee_id = groups.employee_id 
    JOIN employees on groups.employee_id = employees.employee_id
    WHERE
    groups.tanggal = '".$tgl."' 
    AND groups.remark = 'OFF' 
    AND groups.employee_id NOT IN ( SELECT employee_id FROM LEAVES ) 
    ORDER BY
    remark,
    created_at 
    ) AS LOG ORDER BY remark
    ");

    $response = array(
      'status' => true,
      'lists' => $data,
    );
    return Response::json($response);
  }

  public function fetch_detail_sakit(Request $request){

    $tgl = $request->get("tgl");
    $penyakit = $request->get("penyakit");

    if(strlen($request->get('datefrom')) > 0){
      $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    }

    if(strlen($request->get('dateto')) > 0){
      $dateto = date('Y-m-d', strtotime($request->get('dateto')));
    }

    $data = DB::connection('mobile')->select("SELECT quiz_logs.employee_id,
      employees.name,
      employees.department,
      employees.section,
      employees.group
      FROM
      quiz_logs
      LEFT JOIN employees ON quiz_logs.employee_id = employees.employee_id 
      WHERE
      answer_date = '".$tgl."' 
      AND employees.end_date IS NULL 
      AND employees.keterangan IS NULL 
      AND question = '".$penyakit."'
      AND answer = 'Iya'");

    $response = array(
      'status' => true,
      'lists' => $data,
    );
    return Response::json($response);
  }

  public function fetchHealthData(Request $request)
  {
    $tgl = date('Y-m-d', strtotime($request->get('tanggal')));
    // $q =  'select att.*, groups.remark from
    // (select employee_id, `name`, answer_date, SUM(masuk) lat_in, SUM(masuk1) lng_in, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SUM(keluar),null) lat_out, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SUM(keluar2),null) lng_out, SEC_TO_TIME(SUM(time_in)) time_in, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SEC_TO_TIME(SUM(time_out)),null) time_out, TRIM("," FROM GROUP_CONCAT(village)) as village, TRIM("," FROM GROUP_CONCAT(city)) as city from
    // (
    // SELECT employee_id, `name`, answer_date, latitude as masuk, longitude as masuk1, 0 as keluar, 0 as keluar2, id as id_in, 0 as id_out, DATE_FORMAT(created_at, "%H") as jam_in, 0 as jam_out, TIME_TO_SEC(DATE_FORMAT(created_at, "%H:%i")) as time_in, 0 as time_out, village, city FROM quiz_logs
    // WHERE id IN (
    // SELECT MIN(id)
    // FROM quiz_logs
    // GROUP BY employee_id, `name`, answer_date
    // )
    // union all
    // SELECT employee_id, `name`, answer_date, 0 as masuk, 0 as masuk1, latitude as keluar, longitude as keluar2, 0 as id_in, id as id_out, 0 as jam_in,  DATE_FORMAT(created_at, "%H") as jam_out, 0 as time_in,  TIME_TO_SEC(DATE_FORMAT(created_at, "%H:%i")) as time_out, "" as village, "" as city FROM quiz_logs
    // WHERE id IN (
    // SELECT MAX(id)
    // FROM quiz_logs
    // GROUP BY employee_id, `name`, answer_date
    // )
    // ) as semua
    // group by employee_id, `name`, answer_date) as att
    // left join groups on att.employee_id = groups.employee_id AND att.answer_date = groups.tanggal where att.answer_date = "'.$tgl.'"';

    $list = DB::CONNECTION('sunfish')->select("SELECT
      * 
      FROM
      [dbo].[VIEW_AR_YMPI] AS A 
      WHERE
      format ( a.dateTime, 'yyyy-MM-dd' ) = '".$tgl."'");

    $lists = [];

    for ($i=0; $i < count($list); $i++) { 
      $listss = EmployeeSync::where('employee_id',$list[$i]->emp_no)->first();

      $listes = array(
       'employee_id' => $listss->employee_id,
       'name' => $listss->name,
       'date' => date('Y-m-d',strtotime($list[$i]->dateTime)),
       'date_in' => $list[$i]->dateTime,
       'task' => $list[$i]->taskDesc,
       'department' => $listss->department,
       'section' => $listss->section,
       'group' => $listss->group,
       'location' => json_decode($list[$i]->location),
     );
      array_push($lists,$listes);
    }


    $response = array(
      'status' => true,
      'lists' => $lists,
    );
    return Response::json($response);
  }

  public function fetchHealthDataLoc(Request $request)
  {
    $tgl = date('Y-m-d', strtotime($request->get('tanggal')));

    $lists = db::select('SELECT * FROM greatday_attendances where DATE(time_in) = "'.$tgl.'"');

    $response = array(
      'status' => true,
      'lists' => $lists,
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

    $data = DB::connection('mobile')->select("  
      SELECT
      groups.tanggal,
      count( groups.employee_id ) AS total,
      SUM(
      IF
      ( time( log.created_at ) > '07:00:00' AND time( log.created_at ) <= '08:00:00', 1, 0 )) AS lti,
      SUM(
      IF
      ( time( log.created_at ) > '08:00:00' OR log.created_at IS NULL, 1, 0 )) AS abs,
      SUM(
      IF
      ( time( log.created_at ) <= '07:00:00', 1, 0 )) AS prs
      FROM
      groups
      LEFT JOIN (
      SELECT
      employee_id,
      NAME,
      department,
      answer_date,
      min( created_at ) AS created_at 
      FROM
      quiz_logs 
      GROUP BY
      employee_id,
      NAME,
      department,
      answer_date
      ) AS log ON log.employee_id = groups.employee_id and log.answer_date = groups.tanggal
      WHERE
      groups.remark = 'OFF' 
      AND groups.employee_id NOT IN ( SELECT employee_id FROM `leaves` )
      group by groups.tanggal");
      //per tgl
    // $data = DB::connection('mobile')->select("
    //  select distinct answer_date, 
    //  (select count(employee_id) as emp from employees where end_date is null and keterangan is null) as karyawan,
    //  (select count(employee_id) as emp from employees where end_date is null and keterangan is null) - emplo.mengisi as belum,
    //  emplo.mengisi
    //  from
    //  (select answer_date, count(employee_id) as mengisi from
    //  (select answer_date, quiz_logs.employee_id from quiz_logs left join employees on quiz_logs.employee_id = employees.employee_id where keterangan is null
    //  group by employee_id, answer_date) dd
    //  group by answer_date) emplo");

    $data_sakit = DB::connection('mobile')->select("
      SELECT
      cat.answer_date,
      cat.question,
      IFNULL( ans, 0 ) AS count 
      FROM
      ( SELECT DISTINCT answer_date, question FROM quiz_logs WHERE question <> 'Suhu Tubuh' ) cat
      LEFT JOIN (
      SELECT
      answer_date,
      question,
      count( answer ) ans 
      FROM
      quiz_logs
      LEFT JOIN employees ON quiz_logs.employee_id = employees.employee_id 
      WHERE
      answer = 'Iya' 
      AND keterangan IS NULL 
      AND end_date IS NULL 
      GROUP BY
      question,
      answer_date 
      ) AS tidak ON cat.question = tidak.question 
      AND cat.answer_date = tidak.answer_date
      WHERE cat.answer_date >= '2020-04-15'");

    $cat_sakit = DB::connection('mobile')->select("
      select distinct answer_date, question from quiz_logs where question <> 'Suhu Tubuh' and answer_date >= '2020-04-15'");

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

    // $url = "https://locationiq.org/v1/reverse.php?key=pk.456ed0d079b6f646ad4db592aa541ba0&lat=".$lat."&lon=".$long."&format=json";
    // // $url = "https://www.google.com/maps/@".$lat.",".$long."";
    // $curlHandle = curl_init();
    // curl_setopt($curlHandle, CURLOPT_URL, $url);
    // curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    // curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    // curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
    // curl_setopt($curlHandle, CURLOPT_POST, 1);
    // $results = curl_exec($curlHandle);
    // curl_close($curlHandle);

    // $response = array(
    //  'status' => true,
    //  'data' => $results,
    // );
    // return Response::json($response);

    return "<img src='https://maps.locationiq.com/v3/staticmap?key=pk.456ed0d079b6f646ad4db592aa541ba0&center=".$lat.",".$long."&zoom=14&size=1800x1800&format=png&markers=icon:large-green-cutout|".$lat.",".$long."'>";
  }

  public function location(){
    $tglnow = date('Y-m-d');

    if(Auth::user()->role_code == 'HR' || Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'S'){

      return view('mirai_mobile.emp_location',  
        array('title' => 'Resume Employee Location', 
          'title_jp' => ''
        )
      )->with('page', 'Resume Employee Location');
    }

    return view('404',  
      array('message' => 'Silahkan menghubungi bagian HR untuk data absensi MIRAI Mobile.'
    )
    );
  }

  public function fetchLocation()
  {

    $employee_location = db::connection('mobile')->select("SELECT act.answer_date, employees.department, count(act.employee_id) as jumlah from
      (SELECT employee_id, `name`, answer_date, village, city, province FROM quiz_logs
      WHERE id IN (
      SELECT MIN(id)
      FROM quiz_logs
      GROUP BY employee_id, `name`, answer_date
      )) as act
      left join employees on employees.employee_id = act.employee_id
      join (select employee_id, tanggal from groups where remark = 'OFF') all_groups on all_groups.employee_id = act.employee_id AND all_groups.tanggal = act.answer_date
      where act.city <> employees.kota and answer_date >= '2020-04-13' and employees.department <> 'Management Information System'
      group by employees.department, answer_date
      ");

    $period = db::table('weekly_calendars')->where('week_date', '>=', '2020-04-13')->where('week_date', '<=', date('y-m-d'))->select('week_date')->orderBy('week_date', 'desc')->get();

    $response = array(
      'status' => true,
      'period' => $period,
      'emp_location' => $employee_location
    );
    return Response::json($response);
  }

  public function fetchLocationDetail(Request $request)
  {
    if ($request->get('department') != "") {
      $dept = "AND employees.department = '".$request->get('department')."'";
    }else{
      $dept = "";
    }

    if ($request->get('date') != "") {
      $date = "AND answer_date = '".$request->get('date')."'";
    }else{
      $date = "";
    }

    $location_detail = db::connection('mobile')->select("SELECT quiz.employee_id, quiz.`name`, quiz.city, employees.kota, employees.department FROM 
      (SELECT employee_id, `name`, answer_date, village, city, province FROM quiz_logs
      WHERE id IN (
      SELECT MIN(id)
      FROM quiz_logs
      GROUP BY employee_id, `name`, answer_date
      )) as quiz
      left join employees on employees.employee_id = quiz.employee_id
      join (select employee_id, tanggal from groups where remark = 'OFF') all_groups on all_groups.employee_id = quiz.employee_id AND all_groups.tanggal = quiz.answer_date
      where quiz.city <> employees.kota ".$date." ".$dept." AND employees.department <> 'Management Information System'
      ");

    $response = array(
      'status' => true,
      'location_detail' => $location_detail
    );
    return Response::json($response);
  }

  public function fetchLocationDetailAll(Request $request)
  {

    if ($request->get('date') != "") {
      $date = "WHERE answer_date = '".$request->get('date')."'";
    }else{
      $date = "";
    }

    $location_detail = db::connection('mobile')->select("SELECT quiz.answer_date,quiz.employee_id, quiz.`name`, quiz.city, employees.kota, employees.department FROM 
      (SELECT employee_id, `name`, answer_date, village, city, province FROM quiz_logs
      WHERE id IN (
      SELECT MIN(id)
      FROM quiz_logs
      GROUP BY employee_id, `name`, answer_date
      )) as quiz
      left join employees on employees.employee_id = quiz.employee_id
      join (select employee_id, tanggal from groups where remark = 'OFF') all_groups on all_groups.employee_id = quiz.employee_id AND all_groups.tanggal = quiz.answer_date
      ".$date."
      ");

    $response = array(
      'status' => true,
      'location_detail' => $location_detail
    );
    return Response::json($response);
  }

  public function exportList(Request $request){

   $location_detail = db::connection('mobile')->select("SELECT act.answer_date, employees.department, act.employee_id, act.`name`, city, kota from
    (SELECT employee_id, `name`, answer_date, village, city, province FROM quiz_logs
    WHERE id IN (
    SELECT MIN(id)
    FROM quiz_logs
    GROUP BY employee_id, `name`, answer_date
    )) as act
    left join employees on employees.employee_id = act.employee_id
    join (select employee_id, tanggal from groups where remark = 'OFF') all_groups on all_groups.employee_id = act.employee_id AND all_groups.tanggal = act.answer_date
    where act.city <> employees.kota and answer_date >= '2020-04-13'
    and employees.department is not null and employees.department <> 'Management Information System'
    ");

   $data = array(
    'location' => $location_detail
  );

   ob_clean();

   Excel::create('List Lokasi Yang Tidak Sesuai', function($excel) use ($data){
    $excel->sheet('Location', function($sheet) use ($data) {
      return $sheet->loadView('mirai_mobile.location_excel', $data);
    });
  })->export('xlsx');

 }

 public function indication(){
  $tglnow = date('Y-m-d');
  $q = "Select distinct question from quiz_logs";

  return view('mirai_mobile.report_indication',  
    array(
      'title' => 'Resume Gejala Penyakit Karyawan', 
      'title_jp' => '',
      'question' => DB::connection('mobile')->select($q)
    )
  )->with('page', 'Resume Gejala Penyakit Karyawan');
}

public function fetchIndicationData(Request $request)
{
  $tgl = date('Y-m-d', strtotime($request->get('tanggal')));

  $q = "select
  quiz_logs.employee_id,
  quiz_logs.`name`,
  quiz_logs.`department`,
  date( quiz_logs.created_at ) AS date,
  sum(
  IF
  ( question = 'Demam' AND answer = 'iya', 1, 0 )) AS Demam,
  sum(
  IF
  ( question = 'Batuk' AND answer = 'iya', 1, 0 )) AS Batuk,
  sum(
  IF
  ( question = 'Pusing' AND answer = 'iya', 1, 0 )) AS Pusing,
  sum(
  IF
  ( question = 'Tenggorokan Sakit' AND answer = 'iya', 1, 0 )) AS Tenggorokan,
  sum(
  IF
  ( question = 'Sesak Nafas' AND answer = 'iya', 1, 0 )) AS Sesak,
  sum(
  IF
  ( question = 'Indera Perasa & Penciuman Terganggu' AND answer = 'iya', 1, 0 )) AS Indera,
  sum(
  IF
  ( question = 'Pernah Berinteraksi dengan Suspect / Positif COVID-19' AND answer = 'iya', 1, 0 )) AS Kontak,
  ROUND(REPLACE(REPLACE(GROUP_CONCAT(IF(question = 'Suhu Tubuh' AND answer IS NOT NULL, answer, 0 )), 0,''), ',', ''),1) AS Suhu 
  FROM
  quiz_logs
  LEFT JOIN employees ON quiz_logs.employee_id = employees.employee_id 
  WHERE
  keterangan IS NULL 
  AND end_date IS NULL 
  AND answer_date = '".$tgl."'
  GROUP BY
  employee_id,
  `name`,
  date( created_at ),
  department 
  ORDER BY date";

   // having Demam = 1 or Batuk = 1 or Pusing = 1 or Tenggorokan = 1 or Sesak = 1 or Indera = 1 or Kontak = 1 

  $response = array(
    'status' => true,
    'lists' => DB::connection('mobile')->select($q),
  );
  return Response::json($response);
}

public function indexGuestAssessmentReport()
{
  $title = 'Report Guest Assessment Covid-19';
  $title_jp = '';

  return view('mirai_mobile.guest_assessment', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Report Guest Assessment')->with('head','Report Guest Assessment');
}

public function fetchGuestAssessmentReport()
{
  try {
    $guest = DB::SELECT("SELECT * from miraimobile.guest_logs order by created_at desc");

    $response = array(
      'status' => true,
      'guest' => $guest
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

public function fetchGuestAssessmentReportDetail(Request $request)
{
  try {
    $guest = DB::SELECT("
      SELECT * FROM miraimobile.guest_logs where miraimobile.guest_logs.id = '".$request->get('id')."'
      ");

    $response = array(
      'status' => true,
      'guest' => $guest
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

public function indexVendorAssessmentReport()
{
  $title = 'Report Vendor Assessment Covid-19';
  $title_jp = '';

  return view('mirai_mobile.vendor_assessment', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Report Vendor Assessment')->with('head','Report Vendor Assessment');
}

public function fetchVendorAssessmentReport()
{
  try {
    $vendor = DB::SELECT("SELECT * from miraimobile.vendor_logs order by created_at desc");

    $response = array(
      'status' => true,
      'vendor' => $vendor
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