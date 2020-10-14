<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use PDF;
use File;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

use App\Sakurentsu;
use App\EmployeeSync;

class SakurentsuController extends Controller
{
    //==================================//
    //         Upload Sakurentsu        //
    //==================================//
    public function upload_sakurentsu()
    {
        $title = 'Sakurentsu';
        $title_jp = '作連通';

        $employee = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position')->first();

        return view('sakurentsu.report.upload_sakurentsu', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $employee
        ))->with('page', 'Sakurentsu')
        ->with('head', 'Sakurentsu');
    }

    public function upload_file_sakurentsu(Request $request)
    {
      // dd($request)
      try{
        
        $id_user = Auth::id();

        $files = array();
        $file = new Sakurentsu();

        if($request->file('file') != NULL)
        {
           if ($files = $request->file('file'))
            {
                foreach ($files as $file)
                {
                  $nama = $file->getClientOriginalName();
                  $file->move('uploads/sakurentsu', $nama);
                  $data[] = $nama;
                }
            }

            $file->filename = json_encode($data);
        }
        else
        {
          $file->filename = NULL;
        }

        $data2 = Sakurentsu::firstOrNew([
            'sakurentsu_number' => $request->get('sakurentsu_number'),
        ]);

        $data2->applicant = $request->get('applicant');
        $data2->file = $file->filename;
        $data2->upload_date = date('Y-m-d');
        $data2->created_by = $id_user;
        $data2->target_date = $request->get('target_date');
        $data2->status = 'translate';
        $data2->position = 'interpreter';
        $data2->save();

        //Kirim Ke All Interpreter
        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where section = 'Secretary Admin' and employee_id != 'PI9704001'";  
        $mailtoo = DB::select($mails);

        $mailcc = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0008010' or employee_id='PI9905001' or employee_id='PI0812002'";
        $mailtoocc = DB::select($mailcc);

        $isimail = "select * FROM sakurentsus where sakurentsus.id =".$data2->id;
        $sakurentsuisi = db::select($isimail);

        Mail::to($mailtoo)->cc($mailtoocc)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($sakurentsuisi, 'sakurentsu'));

        $response = array(
          'status' => true,
          'message' => 'Berhasil Input Data'
        );

        return Response::json($response);
      }

      catch (Exception $e) {

        $response = array(
          'status' => false,
          'message' => $e->getMessage()
        );
        return Response::json($response);

      }

    }

    public function fetch_sakuretsu(Request $request)
    {
        $data = db::select('
          SELECT * from sakurentsus where deleted_at is null order by id desc
            ');

        $response = array(
            'status' => true,
            'datas' => $data
        );

        return Response::json($response); 
    }

    // Penerjemah

    public function upload_sakurentsu_translate($id)
    {
        $title = 'Sakurentsu';
        $title_jp = '作連通';

        $sakurentsu = Sakurentsu::find($id);

        $employee = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position')->first();

        return view('sakurentsu.report.upload_sakurentsu_translate', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $employee,
            'sakurentsu' => $sakurentsu
        ))->with('page', 'Sakurentsu')
        ->with('head', 'Sakurentsu');
    }

    public function upload_file_sakurentsu_translate(Request $request,$id)
    {

      try{
        
        $id_user = Auth::id();

        $sakurentsu = Sakurentsu::find($id);

        $files = array();
        $file = new Sakurentsu();

        if($request->file('file') != NULL)
        {
           if ($files = $request->file('file'))
            {
                foreach ($files as $file)
                {
                  $nama = $file->getClientOriginalName();
                  $file->move('uploads/sakurentsu/translated', $nama);
                  $data[] = $nama;
                }
            }
            $file->filename = json_encode($data);
        }
        else
        {
          $file->filename = NULL;
        }

        $sakurentsu->translator = $request->get('translator');
        $sakurentsu->file_translate = $file->filename;
        $sakurentsu->translate_date = date('Y-m-d');
        $sakurentsu->position = 'PC';
        $sakurentsu->status = 'approval';
        $sakurentsu->save();

        //Kirim Ke All Mbak Lulu
        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0812002'";  
        $mailtoo = DB::select($mails);

        $mailcc = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0008010' or employee_id = 'PI9905001'";
        $mailtoocc = DB::select($mailcc);

        $isimail = "select * FROM sakurentsus where sakurentsus.id =".$id;
        $sakurentsuisi = db::select($isimail);

        Mail::to($mailtoo)->cc($mailtoocc)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($sakurentsuisi, 'sakurentsu'));

        $response = array(
          'status' => true,
          'message' => 'Berhasil Input Data'
        );

        return Response::json($response);
      }

      catch (Exception $e) {

        $response = array(
          'status' => false,
          'message' => $e->getMessage()
        );
        return Response::json($response);

      }

    }


    public function monitoring(){
      
      $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");
      $bulan = db::select("select DISTINCT MONTH(tgl_permintaan) as bulan, MONTHNAME(tgl_permintaan) as namabulan FROM qc_cpars order by bulan asc;");
      $tahun = db::select("select DISTINCT YEAR(tgl_permintaan) as tahun FROM qc_cpars order by tahun desc");
      $sumber = db::select("select DISTINCT kategori_komplain from qc_cpars where kategori='Eksternal'");
      $dept = db::select("select id, department_name from departments where departments.id not in (1,2,3,4,11)");
      $statuses = db::select("select distinct qc_cpars.status_code, status_name from statuses join qc_cpars on qc_cpars.status_code = statuses.status_code");
      
       return view('sakurentsu.monitoring.sakurentsu_monitoring',  
        array(
        	  'title' => 'Sakurentsu Monitoring', 
            'title_jp' => '作連通進捗管理',
            'fys' => $fys,
            'bulans' => $bulan,
            'years' => $tahun, 
            'departemens' => $dept,
            'status' => $statuses,
            'sumber' => $sumber
          )
        )->with('page', 'CPAR Graph');
    }
}
