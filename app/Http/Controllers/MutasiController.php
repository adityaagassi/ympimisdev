<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Material;
use App\CodeGenerator;
use App\MaterialVolume;
use App\Flo;
use App\FloDetail;
use App\FloLog;
use App\ContainerSchedule;
use App\ContainerAttachment;
use App\User;
use App\Inventory;
use App\EmployeeSync;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use DataTables;
use Yajra\DataTables\Exception;
use Response;
use File;
use Storage;
use Carbon\Carbon;
use App\StampInventory;
use App\LogProcess;
use App\LogTransaction;
use App\ErrorLog;
use App\Mail\SendEmail;
use App\KnockDown;
use App\ShipmentSchedule;
use App\MasterChecksheet;
use Illuminate\Support\Facades\Mail;
use App\PositionCode;
use App\Mutasi;
use App\MutasiAnt;
use App\Navigation;
use App\UploadMutasi;

use PDF;
use Excel;


class MutasiController extends Controller
{
    public function dashboard()
    {
        $dept  = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
       $post    = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
       $section = db::select('SELECT DISTINCT department, section FROM employee_syncs ORDER BY section ASC');
       $group   = db::select('SELECT DISTINCT section, `group` FROM employee_syncs ORDER BY `group` ASC');
       $user    = db::select('SELECT employee_id,name FROM employee_syncs');
       $sub_group   = db::select('SELECT DISTINCT sub_group FROM employee_syncs ORDER BY sub_group ASC');

      // $departement = db::select("select DISTINCT department from employee_syncs");
      $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('department')
      ->first();

      return view('mutasi.dashboard',  
        array(
          'title' => 'Mutation One Department Monitoring & Control', 
          'title_jp' => 'ミューテーション1部門の監視と制御',

          'emp_dept' => $emp_dept,
          'dept' => $dept,
          'post' => $post,
          'group' => $group,
          'section' => $section,
          'sub_group' => $sub_group,
          'user' => $user
      )
    )->with('page', 'Mutasi Satu Departemen');
    }
    public function viewCekEmail()
    {
        $mutasi = MutasiAnt::find($id);
        $isimail = "select id, departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
        return view('mails.mutasi_antar', array(
        ))->with('page', 'Mutasi');
    }
    public function dashboardAnt()
    {
        // return view('mutasi.dashboard_ant', array(
        // ))->with('page', 'Mutasi');
       $dept  = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
       $post    = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
       $section = db::select('SELECT DISTINCT department, section FROM employee_syncs ORDER BY section ASC');
       $group   = db::select('SELECT DISTINCT section, `group` FROM employee_syncs ORDER BY `group` ASC');
       $user    = db::select('SELECT employee_id,name FROM employee_syncs');
       $sub_group   = db::select('SELECT DISTINCT sub_group FROM employee_syncs ORDER BY sub_group ASC');

      // $departement = db::select("select DISTINCT department from employee_syncs");
      $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('department')
      ->first();

      return view('mutasi.dashboard_ant',  
        array(
          'title' => 'Mutation Between Department Monitoring & Control', 
          'title_jp' => '部門の監視と管理の間の変化',
          'emp_dept' => $emp_dept,
          'dept' => $dept,
          'post' => $post,
          'group' => $group,
          'section' => $section,
          'sub_group' => $sub_group,
          'user' => $user
      )
    )->with('page', 'Mutasi Antar Departemen');
    }

     public function get_employee( Request $request)
    {
        try {
            $emp = DB::SELECT("select employee_id, `name`, sub_group, `group`, section, department, position from employee_syncs where
            `employee_id` = '".$request->get('employee_id')."'
            AND `end_date` IS NULL");

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

    public function get_tujuan( Request $request)
    {
        try {
            

            if ($request->get('ke_sub_group') == 'Kosong') {
                $emp = '';
            }else{
                $emp = DB::SELECT("select sub_group, `group`, section, department, position from employee_syncs where
            `sub_group` = '".$request->get('sub_group')."' group by department, section, `group`");
            }

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

    public function get_section( Request $request)
    {
        try {
            if ($request->get('ke__seksi') == 'Kosong') {
                $emp = '';
            }
            else{
                $emp = DB::SELECT("select section, department, position from employee_syncs where
            `section` = '".$request->get('section')."'
            AND `end_date` IS NULL");

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
        }
            
            catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function get_group( Request $request)
    {
        try {
            if ($request->get('ke__group') == 'Kosong') {
                $emp = '';
            }
            else{
                    $emp = DB::SELECT("select section, department, position from employee_syncs where
            `group` = '".$request->get('group')."'
            AND `end_date` IS NULL");

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
            }
               
            catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }
    //reject
    public function rejected(Request $request, $id){
        try{
           $mutasi = Mutasi::find($id);
            $mutasi->status = 'Rejected';
            $mutasi->save();
            
            $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019' or employee_id = 'PI0811002'";  
            $mailtoo = DB::select($mails);

            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen, seksi, ke_seksi from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'rejected_mutasi'));
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }        
    //reject antar departemen
    public function rejectedAnt(Request $request, $id){
        try{
            $mutasi = MutasiAnt::find($id);
            $mutasi->status = 'Rejected';
            $mutasi->save();
            
            $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019' or employee_id = 'PI0811002'";  
            $mailtoo = DB::select($mails);

            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'rejected_mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }        
    // ====================================================================================================================== SATU DEPARTEMEN
    //tampilan dashboard
    public function fetchResumeMutasi(Request $request)
    {   
        $tanggal = date('Y-m-d');
        $dateto = $request->get('dateto');

        if ($dateto == "") {
            $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi', 
                'users.name', 'mutasi_depts.created_by', 'remark')
                ->WHERE('mutasi_depts.deleted_at',null )
                ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"),$tanggal)
                ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
                ->orderBy('mutasi_depts.created_at', 'desc')
                ->get();
        }
        else{
            $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi', 
                'users.name', 'mutasi_depts.created_by', 'remark')
                ->WHERE('mutasi_depts.deleted_at',null )
                ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"),$dateto)
                ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
                ->orderBy('mutasi_depts.created_at', 'desc')
                ->get();
        }

        
        $response = array(
            'status' => true,
            'resumes' => $resumes,
            'dateto' => $dateto
        );
        return Response::json($response);
    }
    //tampilan detail
    public function showApproval($id){

        $mutasi = Mutasi::select('mutasi_tanggal', 'status', 'mutasi_nik', 'mutasi_nama', 'mutasi_bagian', 'mutasi_jabatan1', 'mutasi_rekomendasi', 'mutasi_ke_bagian','mutasi_jabatan' , 'chief_or_foreman', 'manager', 'gm', 'director', db::raw('chief.name as nama_chief'), db::raw('manager.name as nama_manager'), 'id', db::raw('gm.name as nama_gm'))
        ->leftJoin(db::raw('employee_syncs as chief'), 'mutasi_depts.chief_or_foreman', '=', 'chief.employee_id')
        ->leftJoin(db::raw('employee_syncs as manager'), 'mutasi_depts.manager', '=', 'manager.employee_id')
        ->leftJoin(db::raw('employee_syncs as gm'), 'mutasi_depts.gm', '=', 'gm.employee_id')
        ->orderBy('mutasi_depts.created_at', 'desc')
        ->where('mutasi_depts.id', '=', $id)
        ->get();

        
        return view('mutasi.print', array(
            'mutasi' => $mutasi
        ))->with('page', 'Mutasi');
    }
    //new create mutasi
    public function create(){
        $dept = db::select('SELECT DISTINCT department FROM employee_syncs
                            ORDER BY department ASC');

        $sect = db::select('SELECT DISTINCT section FROM employee_syncs
                            ORDER BY section ASC');

        $post = db::select('SELECT DISTINCT position FROM employee_syncs
                            ORDER BY position ASC');

        $user = db::select('SELECT employee_id,name FROM employee_syncs');

        return view('mutasi.create', array(
            'dept' => $dept,
            'sect' => $sect,
            'post' => $post,
            'user' => $user

        ))->with('page', 'Mutasi');
    }

    public function store(Request $request)
    {
            $chief = null;
            $nama_chief = null;
            $posit = null;

            $submission_date = $request->get('submission_date');
            $mutasi_date = date('Y-m-d', strtotime($submission_date . ' + 7 days'));
            
            $departemen = $request->get('department');
            $seksi = $request->get('section');
            $ke_sub_group = $request->get('ke_sub_group');
            $ke_group = $request->get('ke_group');
            $ke_seksi = $request->get('ke_section');
            $position = $request->get('position1');

                    if ($ke_sub_group == 'Kosong' || $ke_sub_group == '') {
                        $sub = ' sub_group is null';
                        $sub_group = null;
                    }else{
                        $sub = " sub_group = '".$ke_sub_group."'";
                        $sub_group = $request->get('ke_sub_group');
                    }

                    if ($ke_group == 'Kosong' || $ke_group == '') {
                        $group = ' and `group` is null';
                        $grp = null;
                    }else{
                        $group = " and `group` = '".$ke_group."'";
                        $grp = $request->get('ke_group');
                    }

                    if ($ke_seksi == 'Kosong' || $ke_seksi == '') {
                        $section = ' and section is null';
                        $sks = null;
                    }else{
                        $section = " and section = '".$ke_seksi."'";
                        $sks = $request->get('ke_section');
                    }

                    if ($departemen == 'Kosong' || $departemen == '') {
                        $dept = ' and department is null';
                        $dpt = null;
                    }else{
                        $dept = " and department = '".$departemen."'";
                        $dpt = $request->get('department');
                    }

                    $post = " and position = '".$position."'";

                $poss = "select position_code, division, department, section, `group`, sub_group, position from position_code where ".$sub." ".$group." ".$section." ".$dept." ".$post." ";
                $pst = db::select($poss);
                if (count($pst) > 0)
                    {
                        foreach ($pst as $pst)
                        {   
                            $posit = $pst->position_code;
                        }
                    }
                    // var_dump($poss);
                    // var_dump($posit);
                    // die();


            $id  = Auth::id();
            $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and department = '".$departemen."' and section = '".$seksi."'");
            
                if ($chf != null)
                {
                    foreach ($chf as $cf)
                    {
                        $chief = $cf->employee_id;
                        $nama_chief = $cf->name;
                    }
                }
                else{
                    if ($request->get('section') == 'Software Section') {
                        $chief = 'PI0103002';
                        $nama_chief = 'Agus Yulianto';
                    }
                }

        try {
        $mutasi = new Mutasi([
                'posisi' => 'chf_asal',
                'nik' => $request->get('employee_id'),
                'nama' => $request->get('name'),
                'sub_group' => $request->get('sub_group'),
                'group' => $request->get('group'),
                'seksi' => $request->get('section'),
                'departemen' => $request->get('department'),
                'jabatan' => $request->get('position'),
                'rekomendasi' => $request->get('rekom'),
                'ke_sub_group' => $sub_group,
                'ke_group' => $grp,
                'ke_seksi' => $sks,
                'ke_jabatan' => $position,
                'tanggal' => $request->get('tanggal'),
                'tanggal_maksimal' => $mutasi_date,
                'alasan' => $request->get('alasan'),
                'chief_or_foreman_asal' => $chief,
                'nama_chief_asal' => $nama_chief,
                'position_code' => $posit,
                'created_by' => $id
            ]);
            $mutasi->save();

            $mails = "select distinct email from mutasi_depts join users on mutasi_depts.chief_or_foreman_asal = users.username where mutasi_depts.id = ".$mutasi->id;
            $mailtoo = DB::select($mails);

            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen, seksi, ke_seksi from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }

    //approval chief asal
    public function mutasi_approvalchief_or_foreman_asal(Request $request, $id){     
        try{
                    $mutasi = Mutasi::find($id);
                    $chief = null;
                    $nama_chief = null;
                    $manager = null;
                    $nama_manager = null;
                    $dgm = null;
                    $nama_dgm = null;

                        $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and department = '".$mutasi->departemen."' and section = '".$mutasi->ke_seksi."'");        
                        if ($chf != null)
                        {
                            foreach ($chf as $cf)
                            {
                                $chief = $cf->employee_id;
                                $nama_chief = $cf->name;
                            }


                        }
                        else{
                            if ($mutasi->ke_seksi == 'Software Section') {
                                $chief = 'PI0103002';
                                $nama_chief = 'Agus Yulianto';
                            }
                            else{
                                $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->departemen."'"); 
                                if ($manager != null)
                                {
                                    foreach ($manager as $mgr)
                                    {
                                        $manager = $mgr->employee_id;
                                        $nama_manager = $mgr->name;
                                    }
                                }
                                else
                                {
                                    if ($mutasi->departemen == 'Production Engineering Department') {
                                        $manager = 'PI0703002';
                                        $nama_manager = 'Susilo Basri Prasetyo';
                                    }
                                    elseif 
                                        ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                                        $manager = 'PI9805006';
                                        $nama_manager = 'Fatchur Rozi';
                                    }
                                    elseif 
                                        ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                                        $manager = 'PI9906002';
                                        $nama_manager = 'Khoirul Umam';
                                    }
                                    else{
                                        $manager = null;
                                        $dgm = 'PI0109004';
                                        $nama_dgm = 'Budhi Apriyanto'; 
                                        }
                                    }
                                }
                            }

                            if ($mutasi->chief_or_foreman_asal == $chief) {
                                $chief = null;
                                $nama_chief = null;

                                $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->departemen."'"); 
                                if ($manager != null)
                                {
                                    foreach ($manager as $mgr)
                                    {
                                        $manager = $mgr->employee_id;
                                        $nama_manager = $mgr->name;
                                    }
                                }
                                else
                                {
                                    if ($mutasi->departemen == 'Production Engineering Department') {
                                        $manager = 'PI0703002';
                                        $nama_manager = 'Susilo Basri Prasetyo';
                                    }
                                    elseif 
                                        ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                                        $manager = 'PI9805006';
                                        $nama_manager = 'Fatchur Rozi';
                                    }
                                    elseif 
                                        ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                                        $manager = 'PI9906002';
                                        $nama_manager = 'Khoirul Umam';
                                    }
                                    else{
                                        $manager = null;
                                        $dgm = 'PI0109004';
                                        $nama_dgm = 'Budhi Apriyanto'; 
                                        }
                                    }

                            }

                            if (($mutasi->chief_or_foreman_asal && $mutasi->chief_or_foreman_tujuan) == null) {
                                $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->departemen."'"); 
                                if ($manager != null)
                                {
                                    foreach ($manager as $mgr)
                                    {
                                        $manager = $mgr->employee_id;
                                        $nama_manager = $mgr->name;
                                    }
                                }
                                else
                                {
                                    if ($mutasi->departemen == 'Production Engineering Department') {
                                        $manager = 'PI0703002';
                                        $nama_manager = 'Susilo Basri Prasetyo';
                                    }
                                    elseif 
                                        ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                                        $manager = 'PI9805006';
                                        $nama_manager = 'Fatchur Rozi';
                                    }
                                    elseif 
                                        ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                                        $manager = 'PI9906002';
                                        $nama_manager = 'Khoirul Umam';
                                    }
                                    else{
                                        $manager = null;
                                        $dgm = 'PI0109004';
                                        $nama_dgm = 'Budhi Apriyanto'; 
                                        }
                                    }
                            }
                    
                        // var_dump($chief); die();


                $mutasi->app_ca = 'Approved';
                $mutasi->date_atasan_asal = date('Y-m-d H-y-s');
                $mutasi->posisi = 'chf_tujuan';
                $mutasi->chief_or_foreman_tujuan = $chief;
                $mutasi->nama_chief_tujuan = $nama_chief;
                $mutasi->manager_tujuan = $manager;
                $mutasi->nama_manager_tujuan = $nama_manager;
                $mutasi->dgm_tujuan = $dgm;
                $mutasi->nama_dgm_tujuan = $nama_dgm;            
                $mutasi->save();

                if ($mutasi->manager_tujuan != null) {
                    $mutasi->posisi = 'mgr';
                    $mutasi->save();

                    $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager_tujuan = users.username where mutasi_depts.id = ".$mutasi->id;
                }
                else if($mutasi->dgm_tujuan != null){
                    $mutasi->posisi = 'dgm';
                    $mutasi->save();

                    $mails = "select distinct email from mutasi_depts join users on mutasi_depts.dgm_tujuan = users.username where mutasi_depts.id = ".$mutasi->id;
                }
                else{
                    $mails = "select distinct email from mutasi_depts join users on mutasi_depts.chief_or_foreman_tujuan = users.username where mutasi_depts.id = ".$mutasi->id;    
                }
                
                $mailtoo = DB::select($mails);
                $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen, seksi, ke_seksi from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
                $mutasi = db::select($isimail);
                Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
                return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');  
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }

    //approval chief tujuan
    public function mutasi_approvalchief_or_foreman_tujuan(Request $request, $id){     
        try{
            $manager = null;
            $nama_manager = null;
            $dgm = null;
            $nama_dgm = null;

            $mutasi = Mutasi::find($id);
            $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->departemen."'"); 
            if ($manager != null)
            {
                foreach ($manager as $mgr)
                {
                    $manager = $mgr->employee_id;
                    $nama_manager = $mgr->name;
                }
            }
            else
            {
                if ($mutasi->departemen == 'Production Engineering Department') {
                    $manager = 'PI0703002';
                    $nama_manager = 'Susilo Basri Prasetyo';
                }
                elseif 
                    ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                    $manager = 'PI9805006';
                    $nama_manager = 'Fatchur Rozi';
                }
                elseif 
                    ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $manager = 'PI9906002';
                    $nama_manager = 'Khoirul Umam';
                }
                else{
                    $manager = null;
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }
            }
            $mutasi->app_ct = 'Approved';
            $mutasi->date_atasan_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'mgr';
            $mutasi->manager_tujuan = $manager;
            $mutasi->nama_manager_tujuan = $nama_manager;
            $mutasi->dgm_tujuan = $dgm;
            $mutasi->nama_dgm_tujuan = $nama_dgm;            
            $mutasi->save();

            if ($mutasi->dgm_tujuan != null) {
                $mutasi->posisi = 'dgm';
                $mutasi->save();

                $mails = "select distinct email from mutasi_depts join users on mutasi_depts.dgm_tujuan = users.username where mutasi_depts.id = ".$mutasi->id;
            }
            else{
                $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager_tujuan = users.username where mutasi_depts.id = ".$mutasi->id;    
            }
            
            $mailtoo = DB::select($mails);
            
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen, seksi, ke_seksi from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }

    //approval manager
    public function mutasi_approvalmanager(Request $request, $id){  
        try{
            $dgm = null;
            $nama_dgm = null;
            $gm = null;
            $nama_gm = null;

            $mutasi = Mutasi::find($id);
            if ($mutasi->dgm_asal == null) {
                if ($mutasi->departemen == 'Woodwind Instrument - Final Assembly (WI-FA) Department' || 
                    $mutasi->departemen == 'Maintenance Department'||
                    $mutasi->departemen == 'Production Engineering Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department'||
                    $mutasi->departemen == 'Quality Assurance Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department'||
                    $mutasi->departemen == 'Educational Instrument (EI) Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }
                elseif($mutasi->departemen == 'Logistic Department'||
                    $mutasi->departemen == 'Procurement Department'||
                    $mutasi->departemen == 'Production Control Department'||
                    $mutasi->departemen == 'Purchasing Control Department'){
                    $gm = 'PI0109004';
                    $nama_gm = 'Budhi Apriyanto';
                }
            }                

            $mutasi->app_mt = 'Approved';
            $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'dgm';
            $mutasi->dgm_tujuan = $dgm;
            $mutasi->nama_dgm_tujuan = $nama_dgm;
            $mutasi->gm_tujuan = $gm;
            $mutasi->nama_gm_tujuan = $nama_gm;            
            $mutasi->save();

            if ($mutasi->dgm_tujuan != null) {
                $mails = "select distinct email from mutasi_depts join users on mutasi_depts.dgm_tujuan = users.username where mutasi_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'gm';
                $mutasi->save();

                $mails = "select distinct email from mutasi_depts join users on mutasi_depts.gm_tujuan = users.username where mutasi_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen, seksi, ke_seksi from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }

    //approval dgm
    public function mutasi_approval_dgm(Request $request, $id){
            try{
            $mutasi = Mutasi::find($id);
            if ($mutasi->dgm_tujuan != null) {
                $gm = 'PI1206001';
                $nama_gm = 'Yukitaka Hayakawa';
            }

            $mutasi->app_dt = 'Approved';
            $mutasi->date_dgm_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'gm';
            $mutasi->gm_tujuan = $gm;
            $mutasi->nama_gm_tujuan = $nama_gm;           
            $mutasi->save();

            $mails = "select distinct email from mutasi_depts join users on mutasi_depts.gm_tujuan = users.username where mutasi_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen, seksi, ke_seksi from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval gm
    public function mutasi_approval_gm(Request $request, $id){
            try{
            $mutasi = Mutasi::find($id);
            $mutasi->app_gt = 'Approved';
            $mutasi->date_gm_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'mgr_hrga';
            $mutasi->manager_hrga = 'PI9707011';
            $mutasi->nama_manager = 'Prawoto';           
            $mutasi->save();

            $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager_hrga = users.username where mutasi_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen, seksi, ke_seksi from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval manager hrga
    public function mutasi_manager_hrga(Request $request, $id){  
        try{
            $mutasi = Mutasi::find($id);

            $mutasi->status = 'All Approved';
            $mutasi->app_m = 'Approved';
            $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
            $mutasi->save();

            if ($mutasi->status == 'All Approved') {
                
                $resumes = Mutasi::select(
                'status', 'posisi', 'nik', 'nama', 'seksi', 'departemen', 'jabatan', 'rekomendasi','ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'mutasi_depts.position_code', 'tanggal', 'tanggal_maksimal', 'alasan', 'created_by', 'remark', 

                'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
                'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
                'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
                'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan', 
                'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan', 
                'manager_hrga', 'nama_manager', 'date_manager_hrga',
                
                'app_ca','app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m',
                db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'))
                ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_depts.nik', '=', 'pegawai.employee_id')
                ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_depts.nik', '=', 'grade.employee_id')
                ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_depts.nik', '=', 'posisi.employee_id')
                ->where('mutasi_depts.id', '=', $id)
                ->get();

                $data = array(
                    'resumes' => $resumes
                );

                // dd($data);

                Excel::create('Mutasi Satu Departemen - '.$id, function($excel) use ($data){
                    $excel->sheet('HR', function($sheet) use ($data) {
                        return $sheet->loadView('mutasi.mutasi_excel', $data);
                    });
                    })->store('xls', public_path('mutasi/satu_departemen'));

                // $nik = $mutasi->nik;
                // $position_code = $mutasi->position_code;
                // $create = $mutasi->created_at;

            //     $upload = create UploadMutasi([
            //     'nik' => $nik,
            //     'position_code' => $position_code,
            //     'created_at' => $create,
            // ]);

                // $upload = UploadMutasi::find($id);

                // $upload->nik = $nik;
                // $upload->position_code = $position_code;
                // $upload->created_at = $create;
                // $upload->save();
                // var_dump($nik);
                // var_dump($position_code);
                // var_dump($create);
                // die();
            }
            

            $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019' or employee_id = 'PI0811002'";  
            $mailtoo = DB::select($mails);

            $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, alasan from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            

            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_satu'));

            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            // var_dump($data);
            // die();
            }
            catch (QueryException $e){
                // dd($e);

            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
        }
    }   

    
    // ====================================================================================================================== ANTAR DEPARTEMEN
    //tampilan dashboard
    public function fetchResumeMutasiAnt(Request $request)
    {   $tanggal = date('Y-m-d');
        $dateto = $request->get('dateto');

        if ($dateto == "") {
            $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi', 
                'users.name', 'mutasi_ant_depts.created_by', 'remark')
                 ->WHERE('mutasi_ant_depts.deleted_at',null )
                 ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m-%d')"),$tanggal)
                 ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
                 ->orderBy('mutasi_ant_depts.created_at', 'desc')
                 ->get();
                  }
        else{
            $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi', 
                 'users.name', 'mutasi_ant_depts.created_by', 'remark')
                 ->WHERE('mutasi_ant_depts.deleted_at',null )
                 ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"),$dateto)
                 ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
                 ->orderBy('mutasi_ant_depts.created_at', 'desc')
                 ->get();
        }
        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response);
    }

    //tampilan detail
    public function showAntApproval($id){
        $mutasi = MutasiAnt::select('status', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'chief_or_foreman_asal', 'date_atasan_asal', 'chief_or_foreman_tujuan', 'date_atasan_tujuan', 'gm_division', 'date_gm', 'manager_hrga', 'date_manager_hrga', 'pres_dir', 'date_pres_dir', 'direktur_hr', 'date_direktur_hr', db::raw('atasan_asal.name as nama_atasan_asal'), db::raw('atasan_tujuan.name as nama_atasan_tujuan'), db::raw('gm.name as nama_gm'), db::raw('manager_hrga.name as nama_manager'), db::raw('pres_dir.name as nama_pres_dir'), db::raw('direktur_hr.name as nama_direktur_hr'))
        ->leftJoin(db::raw('employee_syncs as atasan_asal'), 'mutasi_ant_depts.chief_or_foreman_asal', '=', 'atasan_asal.employee_id')
        ->leftJoin(db::raw('employee_syncs as atasan_tujuan'), 'mutasi_ant_depts.chief_or_foreman_tujuan', '=', 'atasan_tujuan.employee_id')
        ->leftJoin(db::raw('employee_syncs as gm'), 'mutasi_ant_depts.gm_division', '=', 'gm.employee_id')
        ->leftJoin(db::raw('employee_syncs as manager_hrga'), 'mutasi_ant_depts.manager_hrga', '=', 'manager_hrga.employee_id')
        ->leftJoin(db::raw('employee_syncs as pres_dir'), 'mutasi_ant_depts.pres_dir', '=', 'pres_dir.employee_id')
        ->leftJoin(db::raw('employee_syncs as direktur_hr'), 'mutasi_ant_depts.direktur_hr', '=', 'direktur_hr.employee_id')
        ->orderBy('mutasi_ant_depts.created_at', 'desc')
        ->where('mutasi_ant_depts.id', '=', $id)
        ->get();
        return view('mutasi.print_ant', array(
            'mutasi' => $mutasi
        ))->with('page', 'Mutasi');
    }

    public function fetchMutasiDetail(Request $request){

        $resumes = MutasiAnt::select('nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan')
        ->where('mutasi_ant_depts.id', '=', $request->get('id'))
        ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response);
    }

    public function fetchMutasiSatuDetail(Request $request){

        $resumes = Mutasi::select('nik', 'nama','sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi','ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'tanggal', 'alasan')
        ->where('mutasi_depts.id', '=', $request->get('id'))
        ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response);
    }

     public function viewMutasiDetail(Request $request){
        
        // $bulan = $request->get('bulan');
        // $status = $request->get('status');

        // if ($status == "Not Approved") {
        //     $status = "Rejected";
        // }else if($status == "Approved"){
        //     $status = "All Approved";
        // }else if($status == "Proces"){
        //     $status = null;
        // }

        // if ($request->get('dateto') == "") {
        //       $dateto = date('Y-m', strtotime(carbon::now()));
        //   } else {
        //       $dateto = $request->get('dateto');
        //   }

        // $resumes = MutasiAnt::select('status', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan')
        // ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"),$dateto)
        // ->where('status','=',$status)
        // ->get();

        $bulan = $request->get('bulan');
        $status = $request->get('status');

        if ($status == "Rejected") {
            $status = "status = 'Rejected'";
        }else if($status == "Approved"){
            $status = "status = 'All Approved'";
        }else if($status == "Proces"){
            $status = "status is null";
        }

        $dateto = $request->get('dateto');

        if ($dateto != "") {
            $resumes = db::select("
            SELECT
            status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan
            FROM
            mutasi_ant_depts 
            WHERE
            DATE_FORMAT(tanggal, '%Y-%m') = '".$dateto."'
            and ".$status."
            ORDER BY
            tanggal
            ");
          }else{
            $resumes = db::select("
            SELECT
            status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan
            FROM
            mutasi_ant_depts 
            WHERE
            DATE_FORMAT(tanggal, '%M') = '".$bulan."'
            and ".$status."
            ORDER BY
            tanggal
            ");
        }

        $response = array(
            'status' => true,
            'resumes' => $resumes,
            'dateto' => $dateto,
            'status' => $status
        );
        return Response::json($response);
    }

    public function viewMutasiSatuDetail(Request $request){

        $bulan = $request->get('bulan');
        $status = $request->get('status');

        if ($status == "Rejected") {
            $status = "status = 'Rejected'";
        }else if($status == "Approved"){
            $status = "status = 'All Approved'";
        }else if($status == "Proces"){
            $status = "status is null";
        }

        $dateto = $request->get('dateto');

        if ($dateto != "") {
            $resumes = db::select("
            SELECT
            status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
            FROM
            mutasi_depts 
            WHERE
            DATE_FORMAT(tanggal, '%Y-%m') = '".$dateto."'
            and ".$status."
            ORDER BY
            tanggal
            ");
          }else{
            $resumes = db::select("
            SELECT
            status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
            FROM
            mutasi_depts 
            WHERE
            DATE_FORMAT(tanggal, '%M') = '".$bulan."'
            and ".$status."
            ORDER BY
            tanggal
            ");
        }

       

        $response = array(
            'status' => true,
            'resumes' => $resumes,
            'status' => $status
        );
        return Response::json($response);
    }

    //new create mutasi
    public function createAnt(){
        $dept    = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
        $post    = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
        $section = db::select('SELECT DISTINCT section FROM employee_syncs ORDER BY section ASC');
        $group   = db::select('SELECT DISTINCT `group` FROM employee_syncs ORDER BY `group` ASC');
        $user    = db::select('SELECT employee_id,name FROM employee_syncs');
        return view('mutasi.create_ant', array(
            'dept' => $dept,
            'post' => $post,
            'group' => $group,
            'section' => $section,
            'user' => $user
        ))->with('page', 'Mutasi');
    }

    public function storeAnt(Request $request){       
            $manager = null;
            $nama_manager = null;
            $chief = null;
            $nama_chief = null;
            $posit = null;

            $submission_date = $request->get('submission_date');
            $mutasi_date = date('Y-m-d', strtotime($submission_date . ' + 7 days'));

            $department_asal = $request->get('department');
            $seksi = $request->get('section');

            $ke_sub_group = $request->get('ke_sub_group');
            $ke_group = $request->get('ke_group');
            $ke_seksi = $request->get('ke_section');
            $departemen = $request->get('ke_department');
            $position = $request->get('position1');

                    if ($ke_sub_group == 'Kosong' || $ke_sub_group == '') {
                        $sub = ' sub_group is null';
                        $sub_group = null;
                    }else{
                        $sub = " sub_group = '".$ke_sub_group."'";
                        $sub_group = $request->get('ke_sub_group');
                    }

                    if ($ke_group == 'Kosong' || $ke_group == '') {
                        $group = ' and `group` is null';
                        $grp = null;
                    }else{
                        $group = " and `group` = '".$ke_group."'";
                        $grp = $request->get('ke_group');
                    }

                    if ($ke_seksi == 'Kosong' || $ke_seksi == '') {
                        $section = ' and section is null';
                        $sks = null;
                    }else{
                        $section = " and section = '".$ke_seksi."'";
                        $sks = $request->get('ke_section');
                    }

                    if ($departemen == 'Kosong' || $departemen == '') {
                        $dept = ' and department is null';
                        $dpt = null;
                    }else{
                        $dept = " and department = '".$departemen."'";
                        $dpt = $request->get('department');
                    }

                    $post = " and position = '".$position."'";

                $poss = "select position_code, division, department, section, `group`, sub_group, position from position_code where ".$sub." ".$group." ".$section." ".$dept." ".$post." ";
                $pst = db::select($poss);
                if (count($pst) > 0)
                    {
                        foreach ($pst as $pst)
                        {
                            $posit = $pst->position_code;
                        }
                    }
                    // var_dump($poss);
                    // var_dump($posit);
                    // die();
        try {
            $id  = Auth::id();
            $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and department = '".$department_asal."' and section = '".$seksi."'");
            
                if ($chf != null)
                {
                    foreach ($chf as $cf)
                    {
                        $chief = $cf->employee_id;
                        $nama_chief = $cf->name;
                    }
                }
                else{
                    if ($request->get('section') == 'Software Section') {
                        $chief = 'PI0103002';
                        $nama_chief = 'Agus Yulianto';
                    }
                    else{
                        $mgr = db::select("select employee_id, `name` from employee_syncs where position = 'manager' and department = '".$department_asal."'");
                        if ($mgr != null)
                        {
                            foreach ($mgr as $mg)
                            {
                                $manager = $mg->employee_id;
                                $nama_manager = $mg->name;
                            }
                        }
                        elseif ($request->get('department') == 'Production Engineering Department') {
                            $manager = 'PI0703002';
                            $nama_manager = 'Susilo Basri Prasetyo';
                        }
                        elseif 
                            ($request->get('department') == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                            $manager = 'PI9805006';
                            $nama_manager = 'Fatchur Rozi';
                        }
                        elseif 
                            ($request->get('department') == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                            $manager = 'PI9906002';
                            $nama_manager = 'Khoirul Umam';
                        }
                    }
                }

        $mutasi = new MutasiAnt([
                'posisi' => 'chf_asal',
                'nik' => $request->get('employee_id'),
                'nama' => $request->get('name'),
                'sub_group' => $request->get('sub_group'),
                'group' => $request->get('group'),
                'seksi' => $request->get('section'),
                'departemen' => $request->get('department'),
                'jabatan' => $request->get('position'),
                'rekomendasi' => $request->get('rekom'),
                'ke_sub_group' => $request->get('ke_sub_group'),
                'ke_group' => $request->get('ke_group'),
                'ke_seksi' => $request->get('ke_section'),
                'ke_departemen' => $request->get('ke_department'),
                'ke_jabatan' => $request->get('position1'),
                'tanggal' => $request->get('tanggal'),
                'tanggal_maksimal' => $mutasi_date,
                'alasan' => $request->get('alasan'),
                'chief_or_foreman_asal' => $chief,
                'nama_chief_asal' => $nama_chief,
                'manager_asal' => $manager,
                'nama_manager_asal' => $nama_manager,
                'position_code' => $posit,
                'created_by' => $id
            ]);
            $mutasi->save();

            if ($mutasi->chief_or_foreman_asal != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'mgr_asal';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }

            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }

    //approval chief or foreman asal
    public function mutasi_approvalchief_or_foremanAsal(Request $request, $id){
            try{
            $manager = null;
            $nama_manager = null;
            $dgm = null;
            $nama_dgm = null;

            $mutasi = MutasiAnt::find($id);
            $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->departemen."'"); 
            if ($manager != null)
            {
                foreach ($manager as $mgr)
                {
                    $manager = $mgr->employee_id;
                    $nama_manager = $mgr->name;
                }
            }
            else
            {
                if ($mutasi->departemen == 'Production Engineering Department') {
                    $manager = 'PI0703002';
                    $nama_manager = 'Susilo Basri Prasetyo';
                }
                elseif 
                    ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                    $manager = 'PI9805006';
                    $nama_manager = 'Fatchur Rozi';
                }
                elseif 
                    ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $manager = 'PI9906002';
                    $nama_manager = 'Khoirul Umam';
                }
                else{
                    $manager = null;
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }
            }
            $mutasi->app_ca = 'Approved';
            $mutasi->date_atasan_asal = date('Y-m-d H-y-s');
            $mutasi->posisi = 'mgr_asal';
            $mutasi->manager_asal = $manager;
            $mutasi->nama_manager_asal = $nama_manager;
            $mutasi->dgm_asal = $dgm;
            $mutasi->nama_dgm_asal = $nama_dgm;            
            $mutasi->save();

            if ($mutasi->manager_asal != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'dgm_asal';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval manager asal
    public function mutasi_approval_managerAsal(Request $request, $id){
            try{
            $dgm = null;
            $nama_dgm = null;
            $gm = null;
            $nama_gm = null;

            $mutasi = MutasiAnt::find($id);
            if ($mutasi->dgm_asal == null) {
                if ($mutasi->departemen == 'Woodwind Instrument - Final Assembly (WI-FA) Department' || 
                    $mutasi->departemen == 'Maintenance Department'||
                    $mutasi->departemen == 'Production Engineering Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department'||
                    $mutasi->departemen == 'Quality Assurance Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department'||
                    $mutasi->departemen == 'Educational Instrument (EI) Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }
                elseif($mutasi->departemen == 'Logistic Department'||
                    $mutasi->departemen == 'Procurement Department'||
                    $mutasi->departemen == 'Production Control Department'||
                    $mutasi->departemen == 'Purchasing Control Department'){
                    $gm = 'PI0109004';
                    $nama_gm = 'Budhi Apriyanto';
                }
            }                

            $mutasi->app_ma = 'Approved';
            $mutasi->date_manager_asal = date('Y-m-d H-y-s');
            $mutasi->posisi = 'dgm_asal';
            $mutasi->dgm_asal = $dgm;
            $mutasi->nama_dgm_asal = $nama_dgm;
            $mutasi->gm_asal = $gm;
            $mutasi->nama_gm_asal = $nama_gm;            
            $mutasi->save();

            if ($mutasi->dgm_asal != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'gm_asal';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval dgm asal
    public function mutasi_approval_dgmAsal(Request $request, $id){
            try{
            $mutasi = MutasiAnt::find($id);
            if ($mutasi->dgm_asal != null) {
                $gm = 'PI1206001';
                $nama_gm = 'Yukitaka Hayakawa';
            }

            $mutasi->app_da = 'Approved';
            $mutasi->date_dgm_asal = date('Y-m-d H-y-s');
            $mutasi->posisi = 'gm_asal';
            $mutasi->gm_asal = $gm;
            $mutasi->nama_gm_asal = $nama_gm;           
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval gm asal
    public function mutasi_approval_gmAsal(Request $request, $id){
            try{
            $chief = null;
            $nama_chief = null;
            $manager = null;
            $nama_manager = null;

            $mutasi = MutasiAnt::find($id);
            $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and department = '".$mutasi->ke_departemen."' and section = '".$mutasi->ke_seksi."'");
            
                if ($chf != null)
                {
                    foreach ($chf as $cf)
                    {
                        $chief = $cf->employee_id;
                        $nama_chief = $cf->name;
                    }
                }
                elseif($chf != null)
                {
                    if ($request->get('section') == 'Software Section') {
                        $chief = 'PI0103002';
                        $nama_chief = 'Agus Yulianto';
                    }
                    else{
                        $chief = null;
                    }
                }
                elseif($mutasi->chief_or_foreman_tujuan == null){
                    $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->ke_departemen."'"); 
                    if ($manager != null)
                    {
                        foreach ($manager as $mgr)
                        {
                            $manager = $mgr->employee_id;
                            $nama_manager = $mgr->name;
                        }
                    }
                    elseif($manager == null)
                    {
                        if ($mutasi->ke_departemen == 'Production Engineering Department') {
                            $manager = 'PI0703002';
                            $nama_manager = 'Susilo Basri Prasetyo';
                        }
                        elseif 
                            ($mutasi->ke_departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                            $manager = 'PI9805006';
                            $nama_manager = 'Fatchur Rozi';
                        }
                        elseif 
                            ($mutasi->ke_departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                            $manager = 'PI9906002';
                            $nama_manager = 'Khoirul Umam';
                        }
                        elseif 
                            ($mutasi->ke_departemen == 'Management Information System Department') {
                            $manager = 'PI0109004';
                            $nama_manager = 'Budhi Apriyanto';
                        }
                        elseif 
                            ($mutasi->ke_departemen == 'Purchasing Control Department') {
                            $manager = 'PI9807014';
                            $nama_manager = 'Imron Faizal';
                        }
                    }
                }

            $mutasi->app_ga = 'Approved';
            $mutasi->date_gm_asal = date('Y-m-d H-y-s');
            $mutasi->posisi = 'chf_tujuan';
            $mutasi->chief_or_foreman_tujuan = $chief;
            $mutasi->nama_chief_tujuan = $nama_chief;
            $mutasi->manager_tujuan = $manager;
            $mutasi->nama_manager_tujuan = $nama_manager;         
            $mutasi->save();

            if ($mutasi->chief_or_foreman_tujuan != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            else{
                $mutasi->posisi = 'mgr_tujuan';
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);   
            }
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval chief or foreman tujuan
    public function mutasi_approvalchief_or_foremanTujuan(Request $request, $id){
        try{
            $manager = null;
            $nama_manager = null;
            $dgm = null;
            $nama_dgm = null;

            $mutasi = MutasiAnt::find($id);
            $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->ke_departemen."'"); 
            if ($manager != null)
            {
                foreach ($manager as $mgr)
                {
                    $manager = $mgr->employee_id;
                    $nama_manager = $mgr->name;
                }
            }
            else
            {
                if ($mutasi->ke_departemen == 'Production Engineering Department') {
                    $manager = 'PI0703002';
                    $nama_manager = 'Susilo Basri Prasetyo';
                }
                elseif 
                    ($mutasi->ke_departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                    $manager = 'PI9805006';
                    $nama_manager = 'Fatchur Rozi';
                }
                elseif 
                    ($mutasi->ke_departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $manager = 'PI9906002';
                    $nama_manager = 'Khoirul Umam';
                }
                else{
                    $manager = null;
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }
            }
            $mutasi->app_ct = 'Approved';
            $mutasi->date_atasan_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'mgr_tujuan';
            $mutasi->manager_tujuan = $manager;
            $mutasi->nama_manager_tujuan = $nama_manager;
            $mutasi->dgm_tujuan = $dgm;
            $mutasi->nama_dgm_tujuan = $nama_dgm;            
            $mutasi->save();

            if ($mutasi->manager_tujuan != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'dgm_tujuan';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }
    //approval manager tujuan
    public function mutasi_approval_managerTujuan(Request $request, $id){
            try{
            $dgm = null;
            $nama_dgm = null;
            $gm = null;
            $nama_gm = null;

            $mutasi = MutasiAnt::find($id);
            if ($mutasi->dgm_tujuan == null) {
                if ($mutasi->ke_departemen == 'Woodwind Instrument - Final Assembly (WI-FA) Department' || 
                    $mutasi->ke_departemen == 'Maintenance Department'||
                    $mutasi->ke_departemen == 'Production Engineering Department'||
                    $mutasi->ke_departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department'||
                    $mutasi->ke_departemen == 'Quality Assurance Department'||
                    $mutasi->ke_departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department'||
                    $mutasi->ke_departemen == 'Educational Instrument (EI) Department'||
                    $mutasi->ke_departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'||
                    $mutasi->ke_departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }
                elseif($mutasi->ke_departemen == 'Logistic Department'||
                    $mutasi->ke_departemen == 'Procurement Department'||
                    $mutasi->ke_departemen == 'Production Control Department'||
                    $mutasi->ke_departemen == 'Purchasing Control Department'){
                    $gm = 'PI0109004';
                    $nama_gm = 'Budhi Apriyanto';
                }
            }
            else{
                $gm = 'PI1206001';
                $nama_gm = 'Yukitaka Hayakawa';
            }                

            $mutasi->app_mt = 'Approved';
            $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'dgm_tujuan';
            $mutasi->dgm_tujuan = $dgm;
            $mutasi->nama_dgm_tujuan = $nama_dgm;
            $mutasi->gm_tujuan = $gm;
            $mutasi->nama_gm_tujuan = $nama_gm;            
            $mutasi->save();

            if ($mutasi->dgm_tujuan != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'gm_tujuan';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval dgm tujuan
    public function mutasi_approval_dgmTujuan(Request $request, $id){
            try{
            $mutasi = MutasiAnt::find($id);
            if ($mutasi->dgm_asal != null) {
                $gm = 'PI1206001';
                $nama_gm = 'Yukitaka Hayakawa';
            }

            $mutasi->app_dt = 'Approved';
            $mutasi->date_dgm_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'gm_tujuan';
            $mutasi->gm_tujuan = $gm;
            $mutasi->nama_gm_tujuan = $nama_gm;           
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval gm tujuan
    public function mutasi_approval_gmTujuan(Request $request, $id){
            try{
            $mutasi = MutasiAnt::find($id);
            $mutasi->app_gt = 'Approved';
            $mutasi->date_gm_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'mgr_hrga';
            $mutasi->manager_hrga = 'PI9707011';
            $mutasi->nama_manager = 'Prawoto';           
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_hrga = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }
    //approval manager hrga
    public function mutasi_approvalManager_Hrga(Request $request, $id){
        try{
            $mutasi = MutasiAnt::find($id);
            $mutasi->posisi = 'dir_hr';
            $mutasi->app_m = 'Approved';
            $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
            $mutasi->direktur_hr = 'PI9709001';
            $mutasi->nama_direktur_hr = 'Arief Soekamto';
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.direktur_hr = users.username where mutasi_ant_depts.id = ".$mutasi->id;
            $mailtoo = DB::select($mails);
            $isimail = "select id, nama, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval direktur hr
    public function mutasi_approvalDirektur_Hr(Request $request, $id){
        try{
            $mutasi = MutasiAnt::find($id);
            $mutasi->status = 'All Approved';
            $mutasi->app_dir = 'Approved';
            $mutasi->date_direktur_hr = date('Y-m-d H-y-s');
            $mutasi->save();

            $resumes = MutasiAnt::select(
            'id','status', 'posisi', 'nik', 'nama', 'mutasi_ant_depts.sub_group', 'mutasi_ant_depts.group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'mutasi_ant_depts.position_code', 'tanggal', 'alasan', 'created_by', 

            'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
            'manager_asal', 'nama_manager_asal', 'date_manager_asal',
            'dgm_asal', 'nama_dgm_asal', 'date_dgm_asal',
            'gm_asal', 'nama_gm_asal', 'date_gm_asal', 
            'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
            'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
            'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan', 
            'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan', 
            'manager_hrga', 'nama_manager', 'date_manager_hrga',
            'direktur_hr', 'nama_direktur_hr', 'date_direktur_hr', 
            
            'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir',
            db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'), db::raw('code.position_code as code'))
            ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_ant_depts.nik', '=', 'pegawai.employee_id')
            ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_ant_depts.nik', '=', 'grade.employee_id')
            ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_ant_depts.nik', '=', 'posisi.employee_id')
            ->leftJoin(db::raw('employee_syncs as code'), 'mutasi_ant_depts.nik', '=', 'code.employee_id')
            ->where('mutasi_ant_depts.id', '=', $id)
            ->get();
            $data = array(
                'resumes' => $resumes
            );
            if ($mutasi->status == 'All Approved') {
                ob_clean();
                Excel::create('Mutasi Antar Departemen - '.$id, function($excel) use ($data){
                    $excel->sheet('HR', function($sheet) use ($data) {
                        return $sheet->loadView('mutasi.mutasi_ant_excel', $data);
                    });
                    })->store('xls', public_path('mutasi/antar_departemen'));              
            }

            // var_dump($resumes);
            // die();
            $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019' or employee_id = 'PI0811002'";  
            $mailtoo = DB::select($mails);

            $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, alasan from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
                // dd($e);
            }
        }

    // ===========================================================================================================
        //==================================//
    //          Verifikasi mutasi           //
    //==================================//
    public function verifikasi_mutasi_ant(Request $request, $id)
    {
        $mutasi = MutasiAnt::find($id);

        $resumes = MutasiAnt::select(
        'id', 'status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'created_by', 

        'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
        'manager_asal', 'nama_manager_asal', DB::RAW('DATE_FORMAT(date_manager_asal, "%d-%m-%Y") as date_manager_asal'),
        'dgm_asal', 'nama_dgm_asal', DB::RAW('DATE_FORMAT(date_dgm_asal, "%d-%m-%Y") as date_dgm_asal'),
        'gm_asal', 'nama_gm_asal', DB::RAW('DATE_FORMAT(date_gm_asal, "%d-%m-%Y") as date_gm_asal'), 
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
        'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
        'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'), 
        'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'), 
        'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),
        'direktur_hr', 'nama_direktur_hr', DB::RAW('DATE_FORMAT(date_direktur_hr, "%d-%m-%Y") as date_direktur_hr'),
        
        'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir')
        ->where('mutasi_ant_depts.id', '=', $id)
        ->get();

        return view('mutasi.verifikasi.verifikasi_mutasi_ant', array(
            // 'title' => 'Mutasi Antar Departemen Monitoring & Control', 
            // 'title_jp' => '監視・管理',

            'mutasi' => $mutasi,
            'resumes' => $resumes
        ))->with('page', 'Mutasi');
    }
    public function verifikasi_mutasi(Request $request, $id)
    {
        $mutasi = Mutasi::find($id);

        $resumes = Mutasi::select(
        'id', 'status', 'posisi', 'nik', 'nama','sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_seksi', 'ke_sub_group', 'ke_group', 'ke_jabatan', 'tanggal', 'alasan', 'created_by', 

        'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
        'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
        'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'), 
        'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'), 
        'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),
        
        'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m')
        ->where('mutasi_depts.id', '=', $id)
        ->get();

        return view('mutasi.verifikasi.verifikasi_mutasi', array(
            'title' => 'Mutasi Satu Departemen Monitoring & Control', 
            'title_jp' => '監視・管理',

            'mutasi' => $mutasi,
            'resumes' => $resumes
        ))->with('page', 'Mutasi');
    }

    public function report_mutasi_ant(Request $request, $id){
        $mutasi = MutasiAnt::find($id);

        $resumes = MutasiAnt::select(
        'id','status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'created_by', 

        'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
        'manager_asal', 'nama_manager_asal', DB::RAW('DATE_FORMAT(date_manager_asal, "%d-%m-%Y") as date_manager_asal'),
        'dgm_asal', 'nama_dgm_asal', DB::RAW('DATE_FORMAT(date_dgm_asal, "%d-%m-%Y") as date_dgm_asal'),
        'gm_asal', 'nama_gm_asal', DB::RAW('DATE_FORMAT(date_gm_asal, "%d-%m-%Y") as date_gm_asal'), 
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
        'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
        'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'), 
        'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'), 
        'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),
        'direktur_hr', 'nama_direktur_hr', DB::RAW('DATE_FORMAT(date_direktur_hr, "%d-%m-%Y") as date_direktur_hr'), 
        
        'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir')
        ->where('mutasi_ant_depts.id', '=', $id)
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A5', 'landscape');

        $pdf->loadView('mutasi.report.report_ant', array(
            'pr' => $resumes
        ));

        $path = "mutasi/" . $resumes[0]->nik . ".pdf";
        return $pdf->stream("Mutasi ".$resumes[0]->nik. ".pdf");
    }

    public function report_mutasi(Request $request, $id){
        $mutasi = Mutasi::find($id);

        $resumes = Mutasi::select(
        'id', 'status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'tanggal', 'alasan', 'created_by', 

        'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
        'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
        'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'), 
        'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'), 
        'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),
        
        'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m')
        ->where('mutasi_depts.id', '=', $id)
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A5', 'landscape');

        $pdf->loadView('mutasi.report.report', array(
            'title' => 'Mutasi Satu Departemen Monitoring & Control', 
            'title_jp' => '監視・管理',
            
            'pr' => $resumes
        ));

        $path = "mutasi/" . $resumes[0]->nik . ".pdf";
        return $pdf->stream("Mutasi ".$resumes[0]->nik. ".pdf");
    }

    public function finish_ant(Request $request, $id){
        $mutasi = MutasiAnt::find($id);
        $mutasi->remark = '1';
        $mutasi->save();

        return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
    }

    public function finish(Request $request, $id){
        $mutasi = Mutasi::find($id);
        $mutasi->remark = '1';
        $mutasi->save();

        return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
    }

    public function fetchMonitoringMutasiAnt(Request $request)
      {
          $tahun = date('Y');
          $dateto = $request->get('dateto');

          // if ($dateto == "") {
          //     $dateto = date('Y-m', strtotime(carbon::now()));
          // } else {
          //     $dep = '';
          // }
          if ($dateto != "") {
            $data = db::select("
            SELECT
            count( nik ) AS jumlah,
            monthname( tanggal ) AS bulan,
            YEAR ( tanggal ) AS tahun,
            sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
            sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
            sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned 
            FROM
            mutasi_ant_depts 
            WHERE
            mutasi_ant_depts.deleted_at IS NULL 
            AND DATE_FORMAT( tanggal, '%Y-%m' ) = '".$dateto."'
            GROUP BY
            bulan,
            tahun 
            ORDER BY
            tahun,
            MONTH ( tanggal ) ASC
            ");
          }else{
            $data = db::select("
            SELECT
            count( nik ) AS jumlah,
            monthname( tanggal ) AS bulan,
            YEAR ( tanggal ) AS tahun,
            sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
            sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
            sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned 
            FROM
            mutasi_ant_depts 
            WHERE
            mutasi_ant_depts.deleted_at IS NULL
            GROUP BY
            bulan,
            tahun 
            ORDER BY
            tahun,
            MONTH ( tanggal ) ASC
            ");
          }
          

          $response = array(
            'status' => true,
            'datas' => $data,
            'tahun' => $tahun,
            'dateto' => $dateto
        );
          return Response::json($response); 
      }

  public function fetchMonitoringMutasi(Request $request)
      {
          $tahun = date('Y');
          $dateto = $request->get('dateto');

          // if ($dateto == "") {
          //     $dateto = "";
          // } else {
          //     $dateto = $dateto;
          // }

          if ($dateto != "") {
            $data = db::select("
            SELECT
            count( nik ) AS jumlah,
            monthname( tanggal ) AS bulan,
            YEAR ( tanggal ) AS tahun,
            sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
            sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
            sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned 
            FROM
            mutasi_depts 
            WHERE
            mutasi_depts.deleted_at IS NULL 
            AND DATE_FORMAT( tanggal, '%Y-%m' ) = '".$dateto."'
            GROUP BY
            bulan,
            tahun 
            ORDER BY
            tahun,
            MONTH ( tanggal ) ASC
            ");
          }else{
            $data = db::select("
            SELECT
            count( nik ) AS jumlah,
            monthname( tanggal ) AS bulan,
            YEAR ( tanggal ) AS tahun,
            sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
            sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
            sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned 
            FROM
            mutasi_depts 
            WHERE
            mutasi_depts.deleted_at IS NULL
            GROUP BY
            bulan,
            tahun 
            ORDER BY
            tahun,
            MONTH ( tanggal ) ASC
            ");
          }

          

          $response = array(
            'status' => true,
            'datas' => $data,
            'tahun' => $tahun,
            'dateto' => $dateto
        );

          return Response::json($response); 
      }
}