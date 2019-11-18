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
use App\QcCar;
use App\QcCpar;
use App\Department;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class QcCarController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      }

      public function index()
      {
       $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','departments.department_name','employees.name','statuses.status_name')
       ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
       ->join('departments','qc_cpars.department_id','=','departments.id')
       ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
       ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
       ->get();

       $id = Auth::id();

       //get departemen by login

       $user = "select department_name from users join mutation_logs on users.username = mutation_logs.employee_id join departments on departments.department_name = mutation_logs.department where users.id=14 and valid_to IS NULL;";
       $users = DB::select($user);

       return view('qc_car.index', array(
        'cars' => $cars,
        'users' => $users
       ))->with('page', 'CAR');
    }

    public function detail($id)
    {
       $cars = QcCar::find($id);

       $cpar = QcCar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.kategori','qc_cpars.employee_id','qc_cpars.lokasi')
       ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
       ->where('qc_cpars.cpar_no','=',$cars->cpar_no)
       ->get();

       $id = Auth::id();

       //get departemen by login

       $user = "select department_name from users join mutation_logs on users.username = mutation_logs.employee_id join departments on departments.department_name = mutation_logs.department where users.id=14 and valid_to IS NULL;";
       $users = DB::select($user);

       if($cpar[0]->kategori == "Internal"){
          $tabel = "position";
          $position = "Foreman";
       } else if($cpar[0]->kategori == "Eksternal" || $cpar[0]->kategori == "Supplier"){
          $tabel = "grade_name";
          $position = "Staff";
       }
       
       $getpic = "select employees.employee_id, employees.name, departments.department_name from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join departments on departments.department_name = mutation_logs.department join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department='".$users[0]->department_name."' and promotion_logs.".$tabel."='".$position."' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL;";
       $pic = DB::select($getpic);

        return view('qc_car.detail', array(
          'cars' => $cars,
          'cpar' => $cpar,
          'users' => $users,
          'pic' => $pic
        ))->with('page', 'CAR');
    }

    public function detail_action(Request $request, $id)
    {
       try{
          
          $cars = QcCar::find($id);

          $tinjauanall= $request->tinjauan;
          foreach ($tinjauanall as $carstinjauan) {
             $carstinjauan = implode(',', $request->tinjauan);			    
         }
         $id_user = Auth::id();

         $cars->cpar_no = $request->get('cpar_no');
         $cars->tinjauan = $carstinjauan;
         $cars->deskripsi = $request->get('deskripsi');
         $cars->tindakan = $request->get('tindakan');
         $cars->penyebab = $request->get('penyebab');
         $cars->perbaikan = $request->get('perbaikan');
         $cars->created_by = $id_user;
         
         $cars->save();
         return redirect('/index/qc_car/detail/'.$cars->id)->with('status', 'CAR data has been Inserted.')->with('page', 'CAR');
       }
       catch (QueryException $e){
          $error_code = $e->errorInfo[1];
          if($error_code == 1062){
            return back()->with('error', 'CAR error.')->with('page', 'CAR');
        }
        else{
            return back()->with('error', $e->getMessage())->with('page', 'CAR');
        }
      }
    }

    public function print_car($id)
    {
      $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','mutation_logs.section','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','departments.department_name','employees.name','statuses.status_name')
      ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
      ->join('departments','qc_cpars.department_id','=','departments.id')
      ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
      ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
      ->join('mutation_logs','qc_cpars.employee_id','=','mutation_logs.employee_id')
      ->whereNull('mutation_logs.valid_to')
      ->where('qc_cars.id','=',$id)
      ->get();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'potrait');
      $pdf->loadView('qc_car.print_car', array(
        'cars'=>$cars
    ));
      
      return $pdf->stream("CAR ".$id. ".pdf");
    }

    public function coba_print($id)
    {

      $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','qc_cpars.lokasi','mutation_logs.section','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','departments.department_name','employees.name','statuses.status_name')
      ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
      ->join('departments','qc_cpars.department_id','=','departments.id')
      ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
      ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
      ->join('mutation_logs','qc_cpars.employee_id','=','mutation_logs.employee_id')
      ->where('qc_cars.id','=',$id)
      ->whereNull('mutation_logs.valid_to')
      ->get();

      return view('qc_car.print_car', array(
        'cars' => $cars,
      ))->with('page', 'CPAR');
    }

    public function create_pic(Request $request,$id)
    {
        try{
            
            $cars = QcCar::find($id);
            $cars->pic = $request->get('pic'); 
            $cars->save();

            $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code')
           ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
           ->where('qc_cpars.cpar_no','=',$cars->cpar_no)
           ->get();

             foreach ($cpar as $cpar) {
              $idcpar = $cpar->id;
              $cpar->status_code = "5";
              $cpar->save();
             }

            $query = "select qc_cpars.*,departments.department_name,employees.name,statuses.status_name, qc_cars.id as id_car FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cars on qc_cpars.cpar_no = qc_cars.cpar_no where qc_cpars.id='".$idcpar."'";

            $cparemail = db::select($query);

            // kirim email
            $mailpic = "select qc_cars.pic,email FROM qc_cars join users on qc_cars.pic = users.username where qc_cars.id='".$id."'";
            $mailto = db::select($mailpic);

            foreach($mailto as $mail){
              $mailtoo = $mail->email;
            }

            $cars->email_status = "SentStaff";
            $cars->email_send_date = date('Y-m-d');
            $cars->posisi = "staff";
            $cars->save();

            Mail::to($mailtoo)->send(new SendEmail($cparemail, 'cpar'));

            $response = array(
              'status' => true,
              'cars' => $cars,


            );
            return Response::json($response);
        }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'parts' => "PIC already exist"
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'parts' => "PIC not created."
            );
             return Response::json($response);
           }
        }
    }

}
