<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\st_assemblies;
use App\OriginGroup;
use App\CodeGenerator;
use App\MasterChecksheet;
use App\DetailChecksheet;
use App\Inspection;
use App\ShipmentCondition;
use App\AreaInspection;
use App\ShipmentReservation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use File;
use Response;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class CheckSheet extends Controller{

     private $category;
     private $hpl;
     public function __construct(){
          $this->middleware('auth');
          $this->hpl = [
               'ASBELL&BOW',
               'ASBODY',
               'ASFG',
               'ASKEY',
               'ASNECK',
               'ASPAD',
               'ASPART',
               'CASE',
               'CLBARREL',
               'CLBELL',
               'CLFG',
               'CLKEY',
               'CLLOWER',
               'CLPART',
               'CLUPPER',
               'FLBODY',
               'FLFG',
               'FLFOOT',
               'FLHEAD',
               'FLKEY',
               'FLPAD',
               'FLPART',
               'MOUTHPIECE',
               'PN',
               'PN PARTS',
               'RC',
               'TSBELL&BOW',
               'TSBODY',
               'TSFG',
               'TSKEY',
               'TSNECK',
               'TSPART',
               'VENOVA',
          ];
          $this->category = [
               'FG',
               'KD',
               'WIP',
          ];
     }

     public function index(){

          $time = MasterChecksheet::orderBy('created_at', 'desc')
          ->get();
          $carier = ShipmentCondition::orderBy('shipment_condition_code', 'asc')
          ->get();
          return view('Check_Sheet.index', array(
               'time' => $time,
               'carier' => $carier,
               'carier1' => $carier,
          ))->with('page', 'Check Sheet');
     }

     public function show($id){

          $time = MasterChecksheet::find($id);

          $photo = '';
          if(strlen($time->driver_photo) > 0){
               $photo = asset("/files/checksheet/driver/".$time->driver_photo);
          }

          $seal_photo = '';
          if(strlen($time->seal_photo) > 0){
               $seal_photo = asset("/files/checksheet/seal/".$time->seal_photo);
          }

          $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)->get();
          $container = AreaInspection::orderBy('id','ASC')->get();
          $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)->get();

          return view('Check_Sheet.show', array(
               'time' => $time,
               'detail' => $detail,
               'container' => $container,
               'inspection' => $Inspection,
               'photo' => $photo,
               'seal_photo' => $seal_photo,
          ))->with('page', 'Check Sheet');
     }

     public function check($id){

          $time = MasterChecksheet::find($id);

          $photo = '';
          if(strlen($time->driver_photo) > 0){
               $photo = asset("/files/checksheet/driver/".$time->driver_photo);
          }

          $seal_photo = '';
          if(strlen($time->seal_photo) > 0){
               $seal_photo = asset("/files/checksheet/seal/".$time->seal_photo);
          }

          $detail = db::select("select cek.*, IFNULL(inv.quantity,0) as stock from (
               SELECT * from detail_checksheets WHERE id_checkSheet='".$time->id_checkSheet."'
               and deleted_at is null) cek
               LEFT JOIN (
               SELECT material_number, quantity  from inventories WHERE storage_location='FSTK'
          ) as inv on cek.gmc = inv.material_number ORDER BY cek.id asc");

          $container = AreaInspection::orderBy('id','ASC')->get();

          $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)->get();

          return view('Check_Sheet.check', array(
               'time' => $time,
               'detail' => $detail,
               'container' => $container,
               'inspection' => $Inspection,
               'photo' => $photo,
               'seal_photo' => $seal_photo,
          ))->with('page', 'Check Sheet');
     }

     public function print_check($id){

          $time = MasterChecksheet::find($id);
          $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
          ->get();
          $container = AreaInspection::orderBy('id','ASC')
          ->get();
          $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
          ->get();
          return view('Check_Sheet.print', array(
               'time' => $time,
               'detail' => $detail,
               'container' => $container,
               'inspection' => $Inspection,
          ))->with('page', 'Check Sheet');
     }

     public function print_check_surat($id){
          $checksheet = MasterChecksheet::where('master_checksheets.id', '=', $id)
          ->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'master_checksheets.carier')
          ->select('master_checksheets.Stuffing_date', 'master_checksheets.invoice_date', 'master_checksheets.toward', 'master_checksheets.id_checkSheet', 'master_checksheets.no_pol', 'master_checksheets.countainer_number', 'master_checksheets.seal_number', 'shipment_conditions.shipment_condition_name', 'master_checksheets.ct_size')
          ->first();

          $checksheet_details = db::select("SELECT
               invoice AS no_invoice,
               gmc AS material_number,
               goods AS material_description,
               IF
               ( package_qty IS NULL OR package_qty = '', '-', package_qty ) AS no_package,
               IF
               ( package_set IS NULL OR package_set = '', '-', package_set ) AS package,
               qty_qty AS quantity,
               qty_set AS uom 
               FROM
               detail_checksheets 
               WHERE
               id_checkSheet = '".$checksheet->id_checkSheet."'
               AND deleted_at IS NULL");


          $pdf = \App::make('dompdf.wrapper');
          $pdf->getDomPDF()->set_option("enable_php", true);
          $pdf->setPaper('A4', 'potrait');

          $pdf->loadView('Check_Sheet.printsurat', array(
               'checksheet' => $checksheet,
               'checksheet_details' => $checksheet_details,
          ));

          return $pdf->stream("Surat Jalan.pdf");
     }

     public function checkmarking($id){
          $time = MasterChecksheet::find($id);

          $container = AreaInspection::orderBy('id','ASC')->get();
          $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)->get();
          $detail = db::select("select cek.*, IFNULL(inv.quantity,0) as stock from (
               SELECT * from detail_checksheets WHERE id_checkSheet='".$time->id_checkSheet."'
               and deleted_at is null) cek
               LEFT JOIN (
               SELECT material_number, quantity  from inventories WHERE storage_location='FSTK'
          ) as inv on cek.gmc = inv.material_number");

          return view('Check_Sheet.checkmarking', array(
               'time' => $time,
               'detail' => $detail,
               'container' => $container,
               'inspection' => $Inspection,
          ))->with('page', 'Check Sheet');
     }


     public function import(Request $request){

          if($request->hasFile('check_sheet_import')){
               $id = Auth::id();

               $towards = $request->get('toward');
               $toward_length = count($towards);
               $toward = "";

               for($x = 0; $x < $toward_length; $x++) {
                    $toward = $toward."".$towards[$x]."";
                    if($x != $toward_length-1){
                         $toward = $toward."-";
                    }
               }

               $file = $request->file('check_sheet_import');
               $data = file_get_contents($file);
               $code_generator = CodeGenerator::where('note','=','check')->first();
               $number = sprintf("%'.0" . $code_generator->length . "d\n" , $code_generator->index);
               $a = 0;
               $rows = explode("\r\n", $data);
               $code = $number;
               $number1 = sprintf("%'.0" . $code_generator->length . "d" , $code);
               $master = new MasterChecksheet([
                    'id_checkSheet' =>$code_generator->prefix . $number1,
                    'destination' => strtoupper($request->get('destination')),
                    'invoice' => $request->get('invoice'),
                    'countainer_number' => $request->get('countainer_number'),
                    'seal_number' => $request->get('seal_number'),
                    'shipped_from' => $request->get('shipped_from'),
                    'shipped_to' => $request->get('shipped_to'),
                    'carier' => $request->get('carier'),
                    'payment' => $request->get('payment'),
                    'etd_sub' => $request->get('etd_sub'),
                    'no_pol' => $request->get('nopol'),
                    'Stuffing_date' => $request->get('Stuffing_date'),               
                    'invoice_date' => $request->get('invoice_date'),            
                    'toward' => $toward,            
                    'ct_size' => $request->get('ct_size'),  
                    'period' => $request->get('period'),  
                    'ycj_ref_number' => $request->get('ycj_ref_number'),  
                    'created_by' => $id
               ]);

               $Inspection = new Inspection([
                    'id_checksheet' => $code_generator->prefix . $number1,
                    'created_by' => $id
               ]);
               $Inspection->save();
               $master->save();
               $code_generator->index = $code_generator->index+1;
               $code_generator->save();
               foreach ($rows as $row)
               {
                    if (strlen($row) > 0) {
                         $row = explode("\t", $row);
                         if ($row[0] != 'CONSIGNEE & ADDRESS' && $row[0] != ""){
                              if ( $row[5] =='' ) {
                                   $row[5] = '-';
                              }
                              if ( $row[6] =='' ) {
                                   $row[6] = '-';
                              }
                              $detail = new DetailChecksheet([
                                   'id_checkSheet' =>$code_generator->prefix . $number1,
                                   'destination' => $row[0],
                                   'invoice' => $row[1],
                                   'gmc' => $row[2],
                                   'goods' => $row[3],
                                   'marking' => $row[4],
                                   'package_qty' => $row[5],
                                   'package_set' => $row[6],
                                   'qty_qty' => $row[7],
                                   'qty_set' => $row[8],
                                   'created_by' => $id
                              ]);
                              $detail->save();
                         }

                    }
               }



               return redirect('/index/CheckSheet')->with('status', 'New Check Sheet has been imported.')->with('page', 'Check Sheet');

          }else{
               return redirect('/index/CheckSheet')->with('error', 'Please select a file.')->with('page', 'Check Sheet');
          }
     }

     public function importDetail(Request $request){    
          $id = Auth::id();

          $Inspection = new Inspection([
               'id_checksheet' => $request->get('idcs2'),
               'created_by' => $id
          ]);
          $Inspection->save();
          if($request->hasFile('check_sheet_import2')){



               $file = $request->file('check_sheet_import2');
               $data = file_get_contents($file);
               $code_master = $request->get('idcs2');
               $rows = explode("\r\n", $data);
               foreach ($rows as $row)
               {
                    if (strlen($row) > 0) {
                         $row1 = explode("\t", $row);
                         if ($row1[0] != '' && $row1[0] !='CONSIGNEE & ADDRESS'){
                              if ( $row1[5] =='' ) {
                                   $row1[5] = '-';
                              }
                              if ( $row1[6] =='' ) {
                                   $row1[6] = '-';
                              }

                              $detail = new DetailChecksheet([
                                   'id_checkSheet' =>$code_master,
                                   'destination' => $row1[0],
                                   'invoice' => $row1[1],
                                   'gmc' => $row1[2],
                                   'goods' => $row1[3],
                                   'marking' => $row1[4],
                                   'package_qty' => $row1[5],
                                   'package_set' => $row1[6],
                                   'qty_qty' => $row1[7],
                                   'qty_set' => $row1[8],
                                   'created_by' => $id

                              ]); 

                              $detail->save();
                         }
                    }
               }

               return redirect('/index/CheckSheet')->with('status', 'Re - Import Success')->with('page', 'Check Sheet');

          }else{
               return redirect('/index/CheckSheet')->with('error', 'Please select a file.')->with('page', 'Check Sheet');
          }
     }


     public function update(Request $request){
          $DetailChecksheet = DetailChecksheet::find($request->get('id_detail'));
          $DetailChecksheet->confirm = $request->get('confirm');
          $DetailChecksheet->diff = $request->get('diff');
          $DetailChecksheet->save();

          $start = MasterChecksheet::where('id_checkSheet','=',$DetailChecksheet->id_checkSheet)
          ->select('id','start_stuffing', 'period', 'ycj_ref_number')
          ->first();

          if ($start->start_stuffing == null) {
               $start2 = MasterChecksheet::find($start->id);
               $start2->start_stuffing = date('Y-m-d H:i:s');
               $start2->save();
          }

          $start2 = MasterChecksheet::find($start->id);
          $start2->finish_stuffing = date('Y-m-d H:i:s');
          $start2->save();

          if(($start->period != null) && ($start->ycj_ref_number != null)){
               $booking = ShipmentReservation::where('period', $start->period)
               ->where('ycj_ref_number', $start->ycj_ref_number)
               ->where('status', 'BOOKING CONFIRMED')
               ->update([
                    'actual_stuffing' => date('Y-m-d')
               ]);
          }

          $response = array(
               'status' => true,
               'message' => 'Update Success',
               'start' => $start->start_stuffing,
          );
          return Response::json($response);

     }

     public function add(Request $request){
          $id_user = Auth::id();
          $Inspection = Inspection::where('id_checksheet','=', $request->get('id')) 
          ->select('id_checksheet')
          ->first();
          if ($Inspection == '' ){
               $Inspection1 = new Inspection([
                    'id_checksheet' => $request->get('id'),
                    'created_by' => $id_user
               ]);
               $Inspection1->save();
          }

     }


     public function addDetail(Request $request){
          $id_user = Auth::id();
          $Inspection = Inspection::where('id_checksheet','=', $request->get('id'))       
          ->first();
          $a = $request->get('inspection');
          $Inspection->$a = $request->get('confirm');
          $Inspection->created_by = $id_user;
          $Inspection->save();
          $response = array(
               'status' => true,
               'message' => 'Update Success',
          );

     }

     public function addDetail2(Request $request){
          $id_user = Auth::id();
          $Inspection = Inspection::where('id_checksheet','=', $request->get('id'))       
          ->first();
          $a = $request->get('remark');
          $Inspection->$a = $request->get('text');
          $Inspection->created_by = $id_user;
          $Inspection->save();
          $response = array(
               'status' => true,
               'message' => 'Update Success',
          );

     }

     public function check_nomor(Request $request){
          $kolom = $request->get('kolom');

          $Inspection = MasterChecksheet::where('id_checksheet', '=', $request->get('id'))
          ->where(str_replace("closure_", "", $kolom), '=', strtoupper($request->get('isi')))
          ->first();

          if($Inspection){
               $response = array(
                    'status' => true
               );
               return Response::json($response);
          }else{
               $response = array(
                    'status' => false,
                    'message' => 'Not Match'
               );
               return Response::json($response);
          }

     }

     public function nomor(Request $request){
          $id_user = Auth::id();
          $Inspection = MasterChecksheet::where('id_checksheet','=', $request->get('id'))->first();

          $kolom = $request->get('kolom');
          $Inspection->$kolom = strtoupper($request->get('isi'));
          $Inspection->check_by = $id_user;
          $Inspection->save();

          $response = array(
               'status' => true,
               'message' => 'Update Success',
               'id' => $kolom,
               'value' => strtoupper($request->get('isi'))
          );
          return Response::json($response);
     }

     public function bara(Request $request){
          $id_user = Auth::id();
          $Inspection = DetailChecksheet::where('id','=', $request->get('id'))       
          ->first();

          $Inspection->bara = $request->get('isi');
          $Inspection->created_by = $id_user;
          $Inspection->save();

          $start = MasterChecksheet::where('id_checkSheet','=',$Inspection->id_checkSheet)->select('id','start_stuffing', 'period', 'ycj_ref_number')
          ->first();

          if ($start->start_stuffing == null) {
               $start2 = MasterChecksheet::find($start->id);
               $start2->start_stuffing = date('Y-m-d H:i:s');
               $start2->save();
          }

          $start2 = MasterChecksheet::find($start->id);
          $start2->finish_stuffing = date('Y-m-d H:i:s');
          $start2->save();

          if(($start->period != null) && ($start->ycj_ref_number != null)){
               $booking = ShipmentReservation::where('period', $start->period)
               ->where('ycj_ref_number', $start->ycj_ref_number)
               ->where('status', 'BOOKING CONFIRMED')
               ->update([
                    'actual_stuffing' => date('Y-m-d')
               ]);
          }

          $response = array(
               'status' => true,
               'message' => 'Update Success',
          );

     }

     public function getReason(Request $request){
          $reason = MasterChecksheet::where('id_checksheet','=', $request->get('id')) 
          ->select('reason','invoice_date')     
          ->first();

          $response = array(
               'status' => true,
               'message' => 'Update Success',
               'reason' => $reason
          );
          return Response::json($response);
     }

     public function edit(Request $request){
          $id_user = Auth::id();
          $master = MasterChecksheet::where('id_checksheet','=', $request->get('id_chek'))      
          ->first();

          $master->countainer_number = $request->get('countainer_numberE');
          $master->seal_number = $request->get('seal_numberE');
          $master->no_pol = $request->get('nopolE');
          $master->invoice = $request->get('invoiceE');
          $master->destination = strtoupper($request->get('destinationE'));
          $master->shipped_to = $request->get('shipped_toE');
          $master->Stuffing_date = $request->get('Stuffing_dateE');
          $master->invoice_date = $request->get('invoice_dateE');
          $master->etd_sub = $request->get('etd_subE');
          $master->carier = $request->get('carierE');
          $master->payment = $request->get('paymentE');
          $master->reason = $request->get('reason');
          $master->created_by = $id_user;
          $master->save();

          $response = array(
               'status' => true,
               'message' => 'Update Success',
          );

          return redirect('/index/CheckSheet')->with('status', 'Check Sheet has been updated.')->with('page', 'Check Sheet');
     }

     public function marking(Request $request){
          $id_user = Auth::id();
          $Inspection = DetailChecksheet::where('id','=', $request->get('id_detail'))       
          ->first();

          $Inspection->markingcheck = $request->get('marking');
          $Inspection->created_by = $id_user;
          $Inspection->save();
          $response = array(
               'status' => true,
               'message' => 'Update Success',
          );

     }

     public function save(Request $request){
          $id_user = Auth::id();
          $master = MasterChecksheet::where('id_checksheet','=', $request->get('id'))       
          ->first();
          $check = $master->status;

          $master->status = date('Y-m-d H:i:s');
          $master->check_by = $id_user;
          $master->save();

          if(($master->period != null) && ($master->ycj_ref_number != null)){
               $booking = ShipmentReservation::where('period', $master->period)
               ->where('ycj_ref_number', $master->ycj_ref_number)
               ->where('status', 'BOOKING CONFIRMED')
               ->update([
                    'actual_on_board' => date('Y-m-d')
               ]);
          }

          if($check == null){
               self::mailStuffing($master->Stuffing_date);
          }

          return redirect('/index/CheckSheet')->with('status', 'Check Sheet has been saved.')->with('page', 'Check Sheet');

     }

     public function mailStuffing($st_date){
          $mail_to = db::table('send_emails')
          ->where('remark', '=', 'stuffing')
          ->WhereNull('deleted_at')
          ->orWhere('remark', '=', 'superman')
          ->WhereNull('deleted_at')
          ->select('email')
          ->get();

          $query = "SELECT
          IF
          (
          master_checksheets.`status` IS NOT NULL,
          'DEPARTED',
          IF
          ( actual_stuffing.total_actual > 0, 'LOADING', '-' )) AS stats,
          master_checksheets.`status`,
          master_checksheets.id_checkSheet,
          master_checksheets.destination,
          shipment_conditions.shipment_condition_name,
          actual_stuffing.total_plan,
          actual_stuffing.total_actual,
          master_checksheets.reason,
          master_checksheets.start_stuffing,
          master_checksheets.finish_stuffing,
          TIMESTAMPDIFF( MINUTE, master_checksheets.start_stuffing, master_checksheets.finish_stuffing ) AS duration 
          FROM
          master_checksheets
          LEFT JOIN shipment_conditions ON shipment_conditions.shipment_condition_code = master_checksheets.carier
          LEFT JOIN (
          SELECT
          id_checkSheet,
          sum( plan_loading ) AS total_plan,
          sum( actual_loading ) AS total_actual 
          FROM
          (
          SELECT
          id_checkSheet,
          qty_qty AS plan_loading,
          (
          qty_qty /
          IF
          ( package_qty = '-' OR package_qty IS NULL, 1, package_qty ))*
          IF
          ( confirm = 0 AND bara = 0, 1, confirm ) AS actual_loading 
          FROM
          detail_checksheets 
          WHERE
          deleted_at IS NULL 
          ) AS stuffings 
          GROUP BY
          id_checkSheet 
          ) AS actual_stuffing ON actual_stuffing.id_checkSheet = master_checksheets.id_checkSheet 
          WHERE
          master_checksheets.deleted_at IS NULL 
          AND master_checksheets.Stuffing_date = '".$st_date."' 
          ORDER BY
          field(
          stats,
          'LOADING',
          'INSPECTION',
          '-',
          'DEPARTED')";

          $stuffings = db::select($query);

          if($stuffings != null){
               Mail::to($mail_to)->send(new SendEmail($stuffings, 'stuffing'));
          }
     }

     public function delete($id){
          $time = MasterChecksheet::find($id);

          $master = MasterChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
          ->delete();

          $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
          ->delete();


          $response = array(
               'status' => true,
               'message' => 'Delete Success',
          );
          $time2 = MasterChecksheet::orderBy('created_at', 'desc')->get();

          return redirect('/index/CheckSheet')->with('status', 'Check Sheet has been Deleted.')->with('page', 'Check Sheet');
     }

     public function persen($id){


          $ceksheet = DetailChecksheet::where('id_checkSheet', '=', $id);

          $total = $ceksheet->sum('detail_checksheets.package_qty');
          $cek = $ceksheet->sum('detail_checksheets.confirm');


          $response = array(
               'status' => true,
               'total' => $total,
               'cek' => $cek,
          );
          return Response::json($response);
     }

     public function deleteReimport(Request $request){
          $detail = DetailChecksheet::where('id_checkSheet','=' ,$request->get('id'))
          ->delete();

          $Inspection = Inspection::where('id_checkSheet','=' ,$request->get('id'))
          ->delete();


          $response = array(
              'status' => true,
              'message' => 'Update Success',
              'reason' => 'ok'
         );
          return Response::json($response);
     }

     public function importDriverPhoto(Request $request){

          try {
               $directory = 'files\checksheet\driver';

               $file = $request->file('file_datas');
               $name = $file->getClientOriginalName();
               $extension = pathinfo($name, PATHINFO_EXTENSION);
               $filename = $request->input('id_checkSheet').'.'.$extension;

               $file->move($directory,$filename);

               $ck = MasterChecksheet::where('id_checkSheet', '=', $request->input('id_checkSheet'))->first();
               $ck->driver_photo = $filename;
               $ck->save();

               $response = array(
                    'status' => true
               );
               return Response::json($response);


          }catch (\Exception $e) {
               $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
               );
               return Response::json($response);
          }
     }

     public function importSealPhoto(Request $request){

          try {
               $directory = 'files\checksheet\seal';

               $filename = $request->input('id_checkSheet');
               $filename = $request->input('id_checkSheet');



               $file = $request->file('file_datas');
               $name = $file->getClientOriginalName();
               $extension = pathinfo($name, PATHINFO_EXTENSION);
               $filename = $request->input('id_checkSheet').'.'.$extension;

               $file->move($directory,$filename);

               $ck = MasterChecksheet::where('id_checkSheet', '=', $request->input('id_checkSheet'))->first();
               $ck->seal_photo = $filename;
               $ck->save();

               $response = array(
                    'status' => true
               );
               return Response::json($response);


          }catch (\Exception $e) {
               $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
               );
               return Response::json($response);
          }
     }

}
