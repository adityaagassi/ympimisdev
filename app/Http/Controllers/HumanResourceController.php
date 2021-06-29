<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use DataTables;
use Response;
use File;
use PDF;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

use App\UangPekerjaan;
use App\UangSimpati;
use App\UangKeluarga;
use App\EmployeeSync;

class HumanResourceController extends Controller
{
    public function IndexHr(Request $request){
        $user    = db::select('SELECT employee_id,name FROM employee_syncs where `end_date` is null');
        $department = db::select('select distinct department from employee_syncs where department is not null order by department asc');
        $section = DB::SELECT("select DISTINCT(section) from employee_syncs where `end_date` IS NULL order by department");

        return view('human_resource.index_hr',  
            array(
                'title' => 'Dashboard Human Resource', 
                'title_jp' => '??',
                'user' => $user,
                'department' => $department,
                'section' => $section
            )
        )->with('page', 'Human Resource');
    }

    public function GetEmployee(Request $request){
        try {
            // $employee_id_us = [];
            // $user = explode("/", $request->get('employee_id_us'));
            // $user[0];

            // $employee_id_us = $request->get('employee_id_us');


            if ($request->get('employee_id_us') != null) {
                $emp = DB::SELECT("select employee_id, `name`, sub_group, `group`, section, department, position, grade_code from employee_syncs where `employee_id` = '".$request->get('employee_id_us')."' AND `end_date` IS NULL");
            }
            else if ($request->get('employee_id_tk') != null) {
                $emp = DB::SELECT("select employee_id, `name`, sub_group, `group`, section, department, position, grade_code from employee_syncs where `employee_id` = '".$request->get('employee_id_tk')."' AND `end_date` IS NULL");
            }

            // dd($user[]);
            

            if (count($emp) > 0) {
                $response = array(
                    'status' => true,
                    'message' => 'Success',
                    'employee' => $emp

                );
                return Response::json($response);
            }else{
                $response = array(
                    'status' => false,
                    'message' => 'Failed',
                    'employee' => ''
                );
                return Response::json($response);
            }
        }   
            catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function GetSection(Request $request){
        try {
            if ($request->get('department_tp') != null) {
                // $sect = DB::SELECT("select section from employee_syncs where department = '".$request->get('department_tp')."' AND `end_date` IS NULL");
                $sect = db::select("select distinct section from employee_syncs where department = '".$request->get('department_tp')."' and section is not null order by section asc");
            }
            

            if (count($sect) > 0) {
                $response = array(
                    'status' => true,
                    'message' => 'Success',
                    'section' => $sect
                );
                return Response::json($response);
            }else{
                $response = array(
                    'status' => false,
                    'message' => 'Failed',
                    'section' => ''
                );
                return Response::json($response);
            }
        }   
            catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    //------------------ Tunjangan Pekerjaan -----------------

    public function AddUangPekerjaan(Request $request)
    {
        try {
            $new = [];

            $created_by = Auth::id();
            $created_at = date("Y-m-d H:i:s");

            foreach ($request->get('item') as $value) {
                $pkj = new UangPekerjaan;

                $pkj->department = $request->get('department_tp');
                $pkj->seksi = $request->get('section_tp');
                $pkj->bulan = $request->get('bulan_tp');

                $pkj->employee = $value['employee_id_tp'];
                $pkj->in_out = $value['in_out_tp'];
                $pkj->tanggal = $value['tanggal_tp'];
                $pkj->keterangan = $value['keterangan_tp'];

                $pkj->created_by = $created_by;
                $pkj->created_at = $created_at;
                $pkj->updated_at = $created_at;

                // $inv->category = $value['category'];
                // $inv->serial_number = $value['serial'];
                // $inv->description = $value['description'];
                // $inv->project = $value['project'];
                // $inv->location = $value['location'];
                // $inv->qty = $value['quantity'];
                // $inv->used_by = $value['pic'];
                // $inv->receive_date = $request->get('receive_date');
                // $inv->created_by = Auth::id();
                // $inv->condition = 'OK';

                $pkj->save();

                array_push($new, $pkj->id);
            }



            $response = array(
                'status' => true,
                'new' => $new

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

    //------------------ Uang Simpati -----------------

    public function AddUangSimpati(Request $request){

        $created_by = Auth::id();
        $created_at = date("Y-m-d H:i:s");

        $files = '';
        $file = new UangSimpati();
        $fp = '';

        if ($request->file('surat_nikah_us') != NULL)
        {
            if ($files = $request->file('surat_nikah_us'))
            {
                $nama = 'S_NIKAH_'.$request->get('employee_id_us');
                $files->move('hr/uang_simpati', $nama);
                $fp = $nama;
            }
        }
        else if ($request->file('surat_akte_us') != NULL)
        {
            if ($files = $request->file('surat_akte_us'))
            {
                $nama = 'S_AKTE_'.$request->get('employee_id_us');
                $files->move('hr/uang_simpati', $nama);
                $fp = $nama;
            }
        }
        else if ($request->file('surat_kematian_us') != NULL)
        {
            if ($files = $request->file('surat_kematian_us'))
            {
                $nama = 'S_KEMATIAN_'.$request->get('employee_id_us');
                $files->move('hr/uang_simpati', $nama);
                $fp = $nama;
            }
        }
        else if ($request->file('surat_lain_us') != NULL)
        {
            if ($files = $request->file('surat_lain_us'))
            {
                $nama = 'S_LAIN_'.$request->get('employee_id_us');
                $files->move('hr/uang_simpati', $nama);
                $fp = $nama;
            }
        }
        else
        {
            $fp = NULL;
        }

        $db_insert = new UangSimpati([
            'employee'      => $request->employee_id_us,
            'sub_group'     => $request->sub_group_us, 
            'group'         => $request->group_us, 
            'seksi'         => $request->section_us, 
            'department'    => $request->department_us, 
            'jabatan'       => $request->position_us, 
            'permohonan'    => $request->permohonan_us,
            'lampiran'      => $fp, 
            'created_by'    => $created_by,
            'created_at'    => $created_at, 
            'updated_at'    => $created_at
        ]);
        $db_insert->save();

        return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil')->with('page', 'Human Resources');
    }

    //------------------ Tunjangan Keluarga -----------------

    public function AddUangKeluarga(Request $request){

        $created_by = Auth::id();
        $created_at = date("Y-m-d H:i:s");
        $file = new UangKeluarga();

        $pmh = '';
        // $tj = '';

        if ($request->get('isteri_tk') != NULL)
        {
            $pmh = $request->isteri_tk;
        }
        else if ($request->get('anak_tk') != NULL)
        {
            $pmh = 'Tunjangan Anak Ke - '.$request->anak_tk;
        }
        else
        {
            $pmh = NULL;
        }

        $files = '';
        // $file = new UangKeluarga();
        $fp = '';

        if ($request->file('surat_nikah_tk') != NULL)
        {
            if ($files = $request->file('surat_nikah_tk'))
            {
                $nama = 'S_NIKAH_'.$request->get('employee_id_tk');
                $files->move('hr/uang_keluarga', $nama);
                $fp = $nama;
            }
        }
        else if ($request->file('surat_akte_tk') != NULL)
        {
            if ($files = $request->file('surat_akte_tk'))
            {
                $nama = 'S_AKTE_'.$request->get('employee_id_tk');
                $files->move('hr/uang_keluarga', $nama);
                $fp = $nama;
            }
        }
        else if ($request->file('surat_lain_tk') != NULL)
        {
            if ($files = $request->file('surat_lain_tk'))
            {
                $nama = 'S_LAIN_'.$request->get('employee_id_tk');
                $files->move('hr/uang_keluarga', $nama);
                $fp = $nama;
            }
        }
        else
        {
            $fp = NULL;
        }

        $db_insert = new UangKeluarga([
            'employee'      => $request->employee_id_tk,
            'sub_group'     => $request->sub_group_tk, 
            'group'         => $request->group_tk, 
            'seksi'         => $request->section_tk, 
            'department'    => $request->department_tk, 
            'jabatan'       => $request->position_tk, 
            'permohonan'    => $pmh,
            'lampiran'      => $fp, 
            'created_by'    => $created_by,
            'created_at'    => $created_at, 
            'updated_at'    => $created_at
        ]);
        $db_insert->save();

        return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil')->with('page', 'Human Resources');
    }
}
