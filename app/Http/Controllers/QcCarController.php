<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use App\QcCar;
use App\QcCpar;
use App\Department;

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

    	return view('qc_car.index', array('cars' => $cars))->with('page', 'CAR');
    }

    public function detail($id)
    {
    	$cars = QcCar::find($id);

    	return view('qc_car.detail', array(
            'cars' => $cars,
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
			var_dump($carstinjauan);die();
    		$cars->cpar_no = $request->get('cpar_no');
    		$cars->tinjauan = $carstinjauan;
            $cars->deskripsi = $request->get('deskripsi');
            $cars->tindakan = $request->get('tindakan');
            $cars->penyebab = $request->get('penyebab');
            $cars->perbaikan = $request->get('perbaikan');

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
      $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','departments.department_name','employees.name','statuses.status_name')
    	->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
        ->join('departments','qc_cpars.department_id','=','departments.id')
        ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->where('qc_cars.id','=',$id)
        ->get();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'potrait');
      $pdf->loadView('qc_car.print_car', array(
        'cars'=>$cars
      ));
     
      return $pdf->stream();
    }

    public function coba_print($id)
    {

      $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','departments.department_name','employees.name','statuses.status_name')
        ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
        ->join('departments','qc_cpars.department_id','=','departments.id')
        ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->where('qc_cars.id','=',$id)
        ->get();

        return view('qc_car.print_car', array(
            'cars' => $cars,
        ))->with('page', 'CPAR');
    }
}
