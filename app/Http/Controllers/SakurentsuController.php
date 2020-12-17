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
use Carbon\Carbon;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

use App\Sakurentsu;
use App\SakurentsuThreeM;
use App\SakurentsuThreeMDocument;
use App\EmployeeSync;
use App\Department;
use App\User;

class SakurentsuController extends Controller
{

    public function index_sakurentsu()
    {
        $title = 'Sakurentsu';
        $title_jp = '作連通';

        return view('sakurentsu.master.index_sakurentsu', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Sakurentsu List')
        ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em()
    {
        $title = '3M';
        $title_jp = '??';

        return view('sakurentsu.master.index_3m', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', '3M List')
        ->with('head', 'Sakurentsu');
    }

    public function index_form_tiga_em($sk_number)
    {
        $title = '3M Form';
        $title_jp = '??';

        $judul = Sakurentsu::where('sakurentsu_number', '=', $sk_number)->select('sakurentsu_number', 'title', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'))->first();

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();

        return view('sakurentsu.master.index_3m_form', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'judul' => $judul,
            'departemen' => $departments
        ))->with('page', '3M Form')
        ->with('head', 'Sakurentsu');  
    }

    public function index_tiga_em_premeeting($id_three_m)
    {
        $title = '3M Pre-Meeting';
        $title_jp = '??';

        $data = SakurentsuThreeM::where('id', '=', $id_three_m)->select('id', 'sakurentsu_number', 'title', 'product_name', 'proccess_name', 'unit', 'category', 'reason', 'benefit', 'check_before', 'started_date', 'special_items', 'related_department', 'remark', 'att')->first();

        $judul = Sakurentsu::where('sakurentsu_number', '=', $data->sakurentsu_number)->select('sakurentsu_number', 'title', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'))->first();

        $dept = Department::select('department_name','department_shortname')->get();

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();

        return view('sakurentsu.master.index_3m_premeeting', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'judul' => $judul,
            'data' => $data,
            'departemen' => $departments,
            'dept_name' => $dept
        ))->with('page', '3M List')
        ->with('head', 'Sakurentsu');
    }

    public function index_translate_sakurentsu()
    {
        $title = 'Translate List';
        $title_jp = '??';

        return view('sakurentsu.master.index_translate_sakurentsu', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Translate List')
        ->with('head', 'Sakurentsu'); 
    }

    public function index_translate_tiga_em($id)
    {
        $title = 'Translate 3M';
        $title_jp = '??';

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id)->first();

        $judul = Sakurentsu::where('sakurentsu_number', '=', $data_tiga_em->sakurentsu_number)->select('sakurentsu_number', 'title', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'))->first();

        return view('sakurentsu.master.index_3m_translate', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'tiga_m' => $data_tiga_em,
            'judul' => $judul
        ))->with('page', '3M Translate')
        ->with('head', 'Sakurentsu'); 
    }

    public function index_tiga_em_upload($id)
    {
        $title = '3M Upload Document';
        $title_jp = '??';

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id)->first();
        $doc_tiga_em = SakurentsuThreeMDocument::where('form_id', '=', $id)->get();

        return view('sakurentsu.report.upload_3m_document', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'tiga_m' => $data_tiga_em,
            'doc_tiga_m' => $doc_tiga_em
        ))->with('page', '3M Upload Document')
        ->with('head', 'Sakurentsu'); 
    }

    public function index_tiga_em_finalmeeting($id)
    {
        $title = '3M Final Meeting';
        $title_jp = '??';

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id)->first();
        $doc_tiga_em = SakurentsuThreeMDocument::where('form_id', '=', $id)->get();

        $judul = Sakurentsu::where('sakurentsu_number', '=', $data_tiga_em->sakurentsu_number)->select('sakurentsu_number', 'title', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'))->first();

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();
        $dept = Department::select('department_name','department_shortname')->get();

        return view('sakurentsu.master.index_3m_finalmeeting', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'tiga_m' => $data_tiga_em,
            'doc_tiga_m' => $doc_tiga_em,
            'judul' => $judul,
            'departemen' => $departments,
            'dept_name' => $dept
        ))->with('page', '3M FinalMeeting')
        ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_detail($id_three_m)
    {
        $title = '3M Request Form';
        $title_jp = '??';

        $data = SakurentsuThreeM::where('id', '=', $id_three_m)->first();
        $docs = SakurentsuThreeMDocument::where('form_id', '=', $id_three_m)->select('form_id', 'document_name', 'document_description', 'pic', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as target'), db::raw('DATE_FORMAT(finish_date, "%d %M %Y") as finish'))->get(); 

        return view('sakurentsu.report.detail_3m', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'data' => $data,
            'docs' => $docs
        ))->with('page', 'Report Sakurentsu')
        ->with('head', 'Sakurentsu');
    }

    //==================================//
    //         Upload Sakurentsu        //
    //==================================//
    public function upload_sakurentsu()
    {
        $title = 'Sakurentsu';
        $title_jp = '作連通';

        // $employee = EmployeeSync::where('employee_id', Auth::user()->username)
        // ->select('employee_id', 'name', 'position')->first();


        $employee = User::where('username', Auth::user()->username)
        ->select('name')->first();
        
        return view('sakurentsu.report.upload_sakurentsu', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $employee
        ))->with('page', 'Upload Sakurentsu')
        ->with('head', 'Sakurentsu');
    }

    public function upload_file_sakurentsu(Request $request)
    {
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

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $nama = $filename.'_'.date('YmdHi').'.'.$extension;

                        $file->move('uploads/sakurentsu/original', $nama);
                        $data[] = $nama;
                    }
                }

                $file->filename = json_encode($data);
            }
            else
            {
                $file->filename = NULL;
            }

            $number = $request->get('sakurentsu_number1').''.$request->get('sakurentsu_number2').'-'.$request->get('sakurentsu_number3');

            $data2 = Sakurentsu::firstOrNew([
                'sakurentsu_number' => $number,
            ]);

            $data2->applicant = $request->get('applicant');
            $data2->title_jp = $request->get('title_jp');
            $data2->file = $file->filename;
            $data2->upload_date = date('Y-m-d');
            $data2->created_by = $id_user;
            $data2->target_date = $request->get('target_date');
            $data2->category = $request->get('sakurentsu_category');
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
        $data = Sakurentsu::orderBy('id', 'desc')->get();
        // db::select('SELECT * from sakurentsus where deleted_at is null order by id desc');

        $response = array(
            'status' => true,
            'datas' => $data
        );

        return Response::json($response); 
    }

    public function fetch_translate_sakurentsu(Request $request)
    {
        $list = Sakurentsu::where('position', '=', 'interpreter')->select('sakurentsu_number', 'title_jp', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'), 'applicant', db::raw('DATE_FORMAT(upload_date, "%d %M %Y") as tgl_upload'), 'file', 'id')->get();

        $tiga_em = SakurentsuThreeM::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_ms.created_by')
        ->where('remark', '=', '1')
        ->select('id', 'title', 'product_name', 'proccess_name', 'unit', 'category', 'sakurentsu_three_ms.created_at', db::raw('employee_syncs.name as applicant'))
        ->get();

        $response = array(
            'status' => true,
            'datas' => $list,
            'tiga_em' => $tiga_em
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

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $nama = $filename.'_'.date('YmdHi').'.'.$extension;
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

            $sakurentsu->title = $request->get('title');
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

//Detail
    public function detail($id) 
    {
        $title = 'Detail Sakurentsu';
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

    public function detail_sakurentsu($id)
    {
        $title = 'Detail Sakurentsu';
        $title_jp = '作連通';

        $employee = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position')->first();

        $depts = db::select('select department_name, department_shortname from departments');

        return view('sakurentsu.report.detail_sakurentsu', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $employee,
            'depts' => $depts
        ))->with('page', 'Sakurentsu List')
        ->with('head', 'Sakurentsu');
    }

    public function fetch_sakurentsu(Request $request)
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $sk = Sakurentsu::where('id', '=', $request->get('id'))->select('sakurentsu_number', 'title_jp', 'title', 'applicant', 'file_translate', 'file', 'upload_date', 'target_date', 'translate_date', 'translator')->first();

        $response = array(
            'status' => true,
            'datas' => $sk
        );
        return Response::json($response);
    }

    public function post_sakurentsu_type(Request $request)
    {
        if ($request->get('ctg') == '3M' || $request->get('ctg') == 'Trial Request') {
            sakurentsu::where('sakurentsu_number', $request->get('sk_number'))
            ->update([
                'status' => 'determined',
                'position' => 'PIC',
                'position' => $request->get('sort_dept'),
                'pic' => implode(',', $request->get('dept')),
                'category' => $request->get('ctg'),
            ]);
        } else if ($request->get('ctg') == 'Information') {

        }

        $response = array(
            'status' => true
        );
        return Response::json($response);
    }

    public function save_tiga_em_form(Request $request)
    {
        try {
            $sk_number = null;
            if ($request->get("sakurentsu_number")) {
                Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                ->update(['status' => 'created']);
                $sk_number = $request->get("sakurentsu_number");
            }

            if (count($request->file('file_datas')) > 0) {
                $num = 1;
                $files = $request->file('file_datas');
                foreach ($files as $filez)
                {
                    foreach ($filez as $file) {
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $file_name = $filename.'_'.date('YmdHis').$num.'.'.$extension;

                        $file->move('uploads/sakurentsu/three_m/att/', $file_name);

                        $file_names[] = $file_name;
                        $num++;
                    }
                }
                $att = implode(':', $file_names);

            } else {
                $att = null;
            }


            $tiga_em = new SakurentsuThreeM;
            $tiga_em->sakurentsu_number = $sk_number;
            $tiga_em->title = $request->get('title');
            $tiga_em->product_name = $request->get('product');
            $tiga_em->proccess_name = $request->get('proccess');
            $tiga_em->unit = $request->get('unit_name');
            $tiga_em->category = $request->get('category');
            $tiga_em->reason = $request->get('content');
            $tiga_em->benefit = $request->get('benefit');
            $tiga_em->check_before = $request->get('kualitas_before');
            $tiga_em->started_date = $request->get('planned_date');
            $tiga_em->special_items = $request->get('special_item');
            $tiga_em->related_department = $request->get('related_department');
            $tiga_em->att = $att;
            $tiga_em->remark = 1;
            $tiga_em->created_by = Auth::user()->username;

            $tiga_em->save();

            $response = array(
                'status' => true,
                'message' => $num
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }


    public function monitoring(){

        $fys = db::select("SELECT DISTINCT fiscal_year from weekly_calendars");
        $bulan = db::select("SELECT DISTINCT MONTH(tgl_permintaan) as bulan, MONTHNAME(tgl_permintaan) as namabulan FROM qc_cpars order by bulan asc;");
        $tahun = db::select("SELECT DISTINCT YEAR(tgl_permintaan) as tahun FROM qc_cpars order by tahun desc");
        $sumber = db::select("SELECT DISTINCT kategori_komplain from qc_cpars where kategori='Eksternal'");
        $dept = db::select("SELECT id, department_name from departments where departments.id not in (1,2,3,4,11)");
        $statuses = db::select("SELECT distinct qc_cpars.status_code, status_name from statuses join qc_cpars on qc_cpars.status_code = statuses.status_code");

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

    public function fetch_tiga_em(Request $request)
    {
        $sakurentsu_req = Sakurentsu::where('category', '=', "3M")->where('status', '=', 'determined')->select('sakurentsu_number', 'title', 'applicant', 'file_translate', 'upload_date', 'target_date', 'status')->get();

        $three_m_list = SakurentsuThreeM::select('id', 'sakurentsu_number', 'title', 'product_name', 'proccess_name', 'category')->get();

        $response = array(
            'status' => true,
            'requested' => $sakurentsu_req,
            'three_m_list' => $three_m_list
        );
        return Response::json($response);
    }

    public function fetch_tiga_em_document(Request $request)
    {
        DB::connection()->enableQueryLog();
        $docs = SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->where('document_name', '=', $request->get('doc_desc'))->get();

        $con = $docs->count();

        if ($con > 0) {
            $response = array(
                'status' => true,
                'docs' => $docs
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'query' => DB::getQueryLog()
            );
            return Response::json($response);
        }
    }

    public function upload_tiga_em_document(Request $request)
    {
        $file = $request->file('doc_upload');
        $id = $request->get('id_doc_upload');
        $doc_name = $request->get('text_doc_upload');

        // if($request->file('doc_upload'))
        // {
        // $num = 1;
        // foreach ($files as $file)
        // {
        $file_name = $id.'_'.date('Y-m-d His').'.'.$file->getClientOriginalExtension();

        $file->move(base_path('public/uploads/sakurentsu/three_m/doc'), $file_name);

        $three_m = SakurentsuThreeM::where('id', '=', $id)->first();

        $docs = new SakurentsuThreeMDocument;
        $docs->sakurentsu_number = $three_m->sakurentsu_number;
        $docs->form_id = $id;
        $docs->document_name = $doc_name;
        $docs->file_name = $file_name;
        $docs->created_by = Auth::user()->username;
        $docs->save();
        //     $num++;
        // }

        // }

        return redirect()->back()->with(array('alert' => 'Success', 'doc_name' => $doc_name));
    }

    public function post_tiga_em_premeeting(Request $request)
    {
        try{
            $files = array();

            if($request->hasFile('file'))
            {
                $files = $request->file('file');
                $num = 1;
                foreach ($files as $file)
                {
                   $nama = $file->getClientOriginalName();

                   $filename = pathinfo($nama, PATHINFO_FILENAME);
                   $extension = pathinfo($nama, PATHINFO_EXTENSION);

                   $file_name = $filename.'_'.date('YmdHis').$num.'.'.$extension;

                   $file->move('uploads/sakurentsu/three_m/att/', $file_name);
                   $data[] = $file_name;
                   $num++;
               }

               $new_filename = implode(',', $data);
           }
           else
           {
            $new_filename = NULL;
        }

        $related_department = implode(',', $request->get('related_department'));

        SakurentsuThreeM::where('id', $request->get('id'))
        ->update([
            'title' => $request->get('title_name'),
            'product_name' => $request->get('product_name'),
            'proccess_name' => $request->get('proccess_name'),
            'unit' => $request->get('unit_name'),
            'category' => $request->get('category'),
            'reason' => $request->get('isi'),
            'benefit' => $request->get('keuntungan'),
            'check_before' => $request->get('kualitas_before'),
            'started_date' => $request->get('tgl_rencana'),
            'special_items' => $request->get('item_khusus'),
            'bom_change' => $request->get('bom_change'),
            'related_department' => $related_department,
            'remark' => $request->get('stat'),
            'att' => DB::raw('att').'_'.$new_filename,
        ]);

        $need_name = [];

        for ($i=1; $i < 17; $i++) { 
            if ($request->get('doc_'.$i) == 'NEED') {

                SakurentsuThreeMDocument::where('form_id', $request->get('id'))->where('document_name', '=', $request->get('doc_name_'.$i))
                ->update([
                    'document_description' => $request->get('doc_note_'.$i),
                    'target_date' => $request->get('doc_target_'.$i),
                    'finish_date' => $request->get('doc_finish_'.$i),
                    'pic' => $request->get('doc_pic_'.$i)
                ]);

                array_push($need_name, $request->get('doc_name_'.$i));
            }
        }

        SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->whereNotIn('document_name', $need_name)->forceDelete();

        $response = array(
            'status' => true,
            'message' => 'Berhasil Input Data'
        );

        return Response::json($response);
    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function mail_tiga_em_document(Request $request)
{
    $doc_list = SakurentsuThreeMDocument::where('form_id', '=', $request->get('three_m_id'))->select('document_name', 'document_description', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as target'), db::raw('DATE_FORMAT(finish_date, "%d %M %Y") as finish'), 'pic')->get();

        // $arr_pos = ['Manager', 'Foreman','Chief', 'Coordinator', 'Staff', 'Senior Staff'];
    $arr_pos = ['Manager', 'Foreman','Chief', 'Coordinator'];

    $isi = SakurentsuThreeM::where('id', '=', $request->get('three_m_id'))->first();

    $arr_doc_dept = [];

    foreach ($doc_list as $docs) {
        if (count($arr_doc_dept) > 0) {
            if(!in_array($arr_doc_dept, $docs->department, true)){
                array_push($arr_doc_dept, $docs->department);
            }
        } else {
            array_push($arr_doc_dept, $docs->department);
        }
    }

    $email_list = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
    ->whereIn('department', $arr_doc_dept)
    ->whereIn('position', $arr_pos)
    ->whereNull('end_date')
    ->select('email')
    ->get();

    $datas = [
        'documents' => $doc_list,
        'departments' => $arr_doc_dept,
        'tiga_m' => $isi,
        'form_id' => $request->get('three_m_id')
    ];


    Mail::to($email_list)->bcc(['nasiqul.ibat@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($datas, '3m_document'));
}

public function upload_tiga_em_upload(Request $request)
{
    $files = array();

    if($request->file('file') != NULL)
    {
        if ($files = $request->file('file'))
        {
            foreach ($files as $file)
            {
                    // $nama = $file->getClientOriginalName();
                $nama = $request->get('id').'_'.date('Y-m-d His').'.'.$file->getClientOriginalExtension();

                $file->move('uploads/sakurentsu/three_m/doc', $nama);
                $data[] = $nama;
            }
        }

        $filename = json_encode($data);
    }
    else
    {
        $filename = NULL;
    }

    SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->where('document_name', '=', $request->get('doc_name'))
    ->update([
        'finish_date' => date('Y-m-d'),
        'file_name' => implode(',', (array) $filename),
    ]);
}

public function fetch_tiga_em_document_by_id($id)
{
    $docs = SakurentsuThreeMDocument::where('form_id', '=', $id)
    ->select('document_name', 'document_description', 'target_date', 'finish_date', 'pic', 'file_name')
    ->get();

    $response = array(
        'status' => true,
        'docs' => $docs
    );

    return Response::json($response);
}

public function generate_tiga_em_pdf($id_tiga_em)
{
    $data = SakurentsuThreeM::where('id', '=', $id_tiga_em)->first();
    $docs = SakurentsuThreeMDocument::where('form_id', '=', $id_tiga_em)->get();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');
    $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    $pdf->loadView('sakurentsu.report.pdf_3m_stream', array(
        'data' => $data,
        'docs' => $docs,
    ));

    return $pdf->stream("3M Request.pdf");


    // return view('sakurentsu.report.pdf_3m_stream', array(

    // ))->with('page', 'sakure')->with('head', 'Sakurentsu');
}

}


