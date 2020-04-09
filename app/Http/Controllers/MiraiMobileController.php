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

  public function fetch_detail(Request $request){

      $tgl = $request->get("tgl");

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }

      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      $status = $request->get('status');

      if ($status != null) {

          $stat = 'and employee_id = "'.$status.'"';


          if ($status == "Employee Submit") {
            $data = DB::connection('mobile')->select("SELECT DISTINCT(quiz_logs.employee_id),quiz_logs.answer_date,employees.employee_id,employees.name,employees.department,employees.section,employees.group from employees LEFT JOIN quiz_logs on quiz_logs.employee_id = employees.employee_id where quiz_logs.employee_id is not null and employees.end_date is null");
          }

          if ($status == "Employee Not Submit") {
            $data = DB::connection('mobile')->select("SELECT DISTINCT(quiz_logs.employee_id),quiz_logs.answer_date,employees.employee_id,employees.name,employees.department,employees.section,employees.group from employees LEFT JOIN quiz_logs on quiz_logs.employee_id = employees.employee_id where quiz_logs.employee_id is null and employees.end_date is null");
          }
      }else{
          $stat = '';
      }

      return DataTables::of($data)

      ->editColumn('answer_date', function($detail){
        return date('d F Y', strtotime($detail->answer_date));
      })
      ->editColumn('status', function($detail){

      })

      ->rawColumns(['status' => 'status'])
      ->make(true);
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

    $data = DB::connection('mobile')->select("SELECT
      employee_id,
      name,
      DATE(
      NOW()) AS answer_date,
      (
      SELECT
        CONCAT(MIN( created_at ),'/',latitude,'/',longitude) AS masuk 
      FROM
        quiz_logs 
      WHERE
        employee_id = employees.employee_id 
        AND answer_date = DATE(
        NOW())) AS masuk,
      (
      SELECT
      CONCAT(IF
        (
          TIMESTAMPDIFF(
            MINUTE,
            MIN( created_at ),
          MAX( created_at )) < 60,
          NULL,
        IF
          (
            MAX( created_at ) = MIN( created_at ),
            NULL,
          MAX( created_at ))),'/',latitude,'/',longitude) AS keluar 
      FROM
        quiz_logs 
      WHERE
        employee_id = employees.employee_id 
        AND answer_date = DATE(
        NOW())) AS keluar
    FROM
      `employees`
      ".$tgl."");

    $response = array(
      'status' => true,
      'lists' => $data,
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

    $datefrom = date("Y-m-d",  strtotime('-30 days'));
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
      (select count(employee_id) as emp from employees where end_date is null) as karyawan,
      (select count(employee_id) as emp from employees where end_date is null) - emplo.mengisi as belum,
      emplo.mengisi
      from
      (select answer_date, count(employee_id) as mengisi from
      (select answer_date, employee_id from quiz_logs
      group by employee_id, answer_date) dd
      group by answer_date) emplo");

    // $q2 = DB::connection('mobile')->select("SELECT employee_id, `name`, answer_date, latitude as masuk, longitude as masuk1, 0 as keluar, 0 as keluar2 FROM `quiz_logs` group by employee_id, `name`, answer_date");

    $year = date('Y');

    $response = array(
      'status' => true,
      'datas' => $data,
    );

    return Response::json($response);
  }
}
