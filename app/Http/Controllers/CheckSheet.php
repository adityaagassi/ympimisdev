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
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use File;
use Response;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class CheckSheet extends Controller
{
     private $category;
     private $hpl;
     public function __construct()
     {
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
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
public function index()
{

     $time = MasterChecksheet::orderBy('created_at', 'desc')
     ->get();
     $carier = ShipmentCondition::orderBy('shipment_condition_code', 'asc')
     ->get();
     return view('Check_Sheet.index', array(
          'time' => $time,
          'carier' => $carier,
          'carier1' => $carier,
     ))->with('page', 'Check Sheet');
//
}

public function show($id)
{

     $time = MasterChecksheet::find($id);

     $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
     ->get();
     $container = AreaInspection::orderBy('id','ASC')
     ->get();
     $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
     ->get();

     return view('Check_Sheet.show', array(
          'time' => $time,
          'detail' => $detail,
          'container' => $container,
          'inspection' => $Inspection,
     ))->with('page', 'Check Sheet');
//
}

public function check($id)
{

     $time = MasterChecksheet::find($id);
     // $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
     // ->get();
     $details ="select cek.*, IFNULL(inv.quantity,0) as stock from (
     SELECT * from detail_checksheets WHERE id_checkSheet='".$time->id_checkSheet."'
      and deleted_at is null) cek
     LEFT JOIN (
     SELECT material_number, quantity  from inventories WHERE storage_location='FSTK'
) as inv on cek.gmc = inv.material_number ORDER BY cek.id asc";
$container = AreaInspection::orderBy('id','ASC')
->get();
$Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
->get();

$detail = db::select($details);
return view('Check_Sheet.check', array(
     'time' => $time,
     'detail' => $detail,
     'container' => $container,
     'inspection' => $Inspection,
))->with('page', 'Check Sheet');
//
}
public function print_check($id)
{

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
//
}

public function print_check_surat($id)
{

     $time = MasterChecksheet::find($id);
     // $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
     // ->get();

     $details ="select cek.*, IFNULL(inv.quantity,0) as stock from (
     SELECT * from detail_checksheets WHERE id_checkSheet='".$time->id_checkSheet."'
     ) cek
     LEFT JOIN (
     SELECT material_number, quantity  from inventories WHERE storage_location='FSTK'
) as inv on cek.gmc = inv.material_number";


$container = AreaInspection::orderBy('id','ASC')
->get();
$Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
->get();

$detail = db::select($details);
return view('Check_Sheet.printsurat', array(
     'time' => $time,
     'detail' => $detail,
     'container' => $container,
     'Inspection' => $Inspection,
))->with('page', 'Check Sheet');
//
}

public function checkmarking($id)
{
// $get = $request->get('stamp_number_reprint');
     $time = MasterChecksheet::find($id);
     // $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
     // ->get();
     $details ="select cek.*, IFNULL(inv.quantity,0) as stock from (
     SELECT * from detail_checksheets WHERE id_checkSheet='".$time->id_checkSheet."'
     and deleted_at is null) cek
     LEFT JOIN (
     SELECT material_number, quantity  from inventories WHERE storage_location='FSTK'
) as inv on cek.gmc = inv.material_number";

$container = AreaInspection::orderBy('id','ASC')
->get();
$Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
->get();
$detail = db::select($details);

return view('Check_Sheet.checkmarking', array(
     'time' => $time,
     'detail' => $detail,
     'container' => $container,
     'inspection' => $Inspection,
))->with('page', 'Check Sheet');
//
}

// public function import(Request $request)
// {
//   $masterid = MasterChecksheet::orderBy('id','desc')
//   ->first();
//   $masterid_fix = $masterid->id;
//   if($request->hasFile('check_sheet_import')){
//           // st_assemblies::truncate();

//     $id = Auth::id();

//     $file = $request->file('check_sheet_import');
//     $data = file_get_contents($file);
//     $code_generator = CodeGenerator::where('note','=','check')->first();
//     $number = sprintf("%'.0" . $code_generator->length . "d\n" , $code_generator->index);
//     $a = 0;
//     $rows = explode("\r\n", $data);
//     foreach ($rows as $row)
//     {
//       if (strlen($row) > 0) {
//         $a++;

//         $row = explode("\t", $row);
//         if ($row[0] != ''){
//           $code = $number+$a;
//           $number1 = sprintf("%'.0" . $code_generator->length . "d" , $code);
//           $master = new MasterChecksheet([
//             'id_input' => $row[0],
//             'id_checkSheet' =>$code_generator->prefix . $number1,
//             'destination' => $row[1],
//             'invoice' => $row[2],
//             'countainer_number' => $row[3],
//             'seal_number' => $row[4],
//             'shipped_from' => $row[5],
//             'shipped_to' => $row[6],
//             'carier' => $row[7],
//             'payment' => $row[8],
//             'etd_sub' => $row[9],
//             'Stuffing_date' => $row[10],
//             'created_by' => $id
//           ]);

//           $Inspection = new Inspection([
//             'id_checksheet' => $code_generator->prefix . $number1,
//             'created_by' => $id
//           ]);

//           $Inspection->save();
//           $master->save();
//           $code_generator->index = $code_generator->index+1;
//           $code_generator->save();

//         }

//       }
//     }

//     foreach ($rows as $row1)
//     {
//       if (strlen($row1) > 0) {
//         $a++;

//         $row1 = explode("\t", $row1);
//         if ($row1[0] != ''){
//           $code = $number+$a;
//             // $date1 = MasterChecksheet::select(DB::raw('ADDTIME(created_at,-10)'))
//             // ->orderBy('created_at','desc')
//             // ->first();
//           $master1 = MasterChecksheet::where('id_input','=',$row1[11])
//           ->where('id','>' ,$masterid_fix)
//           ->first();
//           $code_master = $master1->id_checkSheet;
//           $detail = new DetailChecksheet([
//             'id_checkSheet' =>$code_master,
//             'destination' => $row1[12],
//             'invoice' => $row1[13],
//               // 'countainer_number' => $row1[14],
//             'gmc' => $row1[14],
//             'goods' => $row1[15],
//             'marking' => $row1[16],
//             'package_qty' => $row1[17],
//             'package_set' => $row1[18],
//             'qty_qty' => $row1[19],
//             'qty_set' => $row1[20],
//             'created_by' => $id
//           ]);
//           $detail->save();
//         }
//         if ($row1[0] == ''){
//             //   $date1 = MasterChecksheet::select(DB::raw('ADDTIME(created_at,-10)'))
//             // ->orderBy('created_at','desc')
//             // ->first();
//           $master1 = MasterChecksheet::where('id_input','=',$row1[11])
//           ->where('id','>' ,$masterid_fix)
//           ->first();
//           $code_master = $master1->id_checkSheet;
//           $detail = new DetailChecksheet([
//             'id_checkSheet' =>$code_master,
//             'destination' => $row1[12],
//             'invoice' => $row1[13],
//               // 'countainer_number' => $row1[14],
//             'gmc' => $row1[14],
//             'goods' => $row1[15],
//             'marking' => $row1[16],
//             'package_qty' => $row1[17],
//             'package_set' => $row1[18],
//             'qty_qty' => $row1[19],
//             'qty_set' => $row1[20],
//             'created_by' => $id

//           ]);  

//           $detail->save();
//         }
//       }
//     }

//     return redirect('/index/CheckSheet')->with('status', 'New Check Sheet has been imported.')->with('page', 'Check Sheet');

//   }
//   else
//   {
//     return redirect('/index/CheckSheet')->with('error', 'Please select a file.')->with('page', 'Check Sheet');
//   }
// }

public function import(Request $request)
{
// $masterid = MasterChecksheet::orderBy('id','desc')
// ->first();
// $masterid_fix = $masterid->id;
     if($request->hasFile('check_sheet_import')){
// st_assemblies::truncate();

          $id = Auth::id();

          $file = $request->file('check_sheet_import');
          $data = file_get_contents($file);
          $code_generator = CodeGenerator::where('note','=','check')->first();
          $number = sprintf("%'.0" . $code_generator->length . "d\n" , $code_generator->index);
          $a = 0;
          $rows = explode("\r\n", $data);
          $code = $number;
          $number1 = sprintf("%'.0" . $code_generator->length . "d" , $code);
          $master = new MasterChecksheet([
// 'id_input' => $row[0],
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
                    if ($row[0] != '' && $row[0] !='CONSIGNEE & ADDRESS'){
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
// 'countainer_number' => $row1[2],
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

     }
     else
     {
          return redirect('/index/CheckSheet')->with('error', 'Please select a file.')->with('page', 'Check Sheet');
     }
}

public function importDetail(Request $request)
{    
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

     }
     else
     {
          return redirect('/index/CheckSheet')->with('error', 'Please select a file.')->with('page', 'Check Sheet');
     }
}


public function update(Request $request){
     $DetailChecksheet = DetailChecksheet::find($request->get('id_detail'));
     $DetailChecksheet->confirm = $request->get('confirm');
     $DetailChecksheet->diff = $request->get('diff');
     $DetailChecksheet->save();

     $start = MasterChecksheet::where('id_checkSheet','=',$DetailChecksheet->id_checkSheet)->select('id','start_stuffing')
     ->first();

     if ($start->start_stuffing == null) {
          $start2 = MasterChecksheet::find($start->id);
          $start2->start_stuffing = date('Y-m-d H:i:s');
          $start2->save();
     }

     $start2 = MasterChecksheet::find($start->id);
     $start2->finish_stuffing = date('Y-m-d H:i:s');
     $start2->save();

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

public function nomor(Request $request){
     $id_user = Auth::id();
     $Inspection = MasterChecksheet::where('id_checksheet','=', $request->get('id'))       
     ->first();
     $a = $request->get('kolom');
     $Inspection->$a = $request->get('isi');
     $Inspection->check_by = $id_user;
     $Inspection->save();
     $response = array(
          'status' => true,
          'message' => 'Update Success',
     );

}

public function bara(Request $request){
     $id_user = Auth::id();
     $Inspection = DetailChecksheet::where('id','=', $request->get('id'))       
     ->first();

     $Inspection->bara = $request->get('isi');
     $Inspection->created_by = $id_user;
     $Inspection->save();

     $start = MasterChecksheet::where('id_checkSheet','=',$Inspection->id_checkSheet)->select('id','start_stuffing')
     ->first();

     if ($start->start_stuffing == null) {
          $start2 = MasterChecksheet::find($start->id);
          $start2->start_stuffing = date('Y-m-d H:i:s');
          $start2->save();
     }

     $start2 = MasterChecksheet::find($start->id);
     $start2->finish_stuffing = date('Y-m-d H:i:s');
     $start2->save();

     $response = array(
          'status' => true,
          'message' => 'Update Success',
     );

}

public function getReason(Request $request)
{
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

     $query = "select if(master_checksheets.`status` is not null, 'DEPARTED', if(actual_stuffing.total_actual<actual_stuffing.total_plan and actual_stuffing.total_actual>0, 'LOADING', '-')) as stats, master_checksheets.`status`, master_checksheets.id_checkSheet, master_checksheets.destination, shipment_conditions.shipment_condition_name, actual_stuffing.total_plan, actual_stuffing.total_actual, master_checksheets.reason, master_checksheets.start_stuffing, master_checksheets.finish_stuffing from master_checksheets left join shipment_conditions on shipment_conditions.shipment_condition_code = master_checksheets.carier 
     left join
     (
     select id_checkSheet, sum(plan_loading) as total_plan, sum(actual_loading) as total_actual from (
     select id_checkSheet, qty_qty as plan_loading, (qty_qty/if(package_qty = '-' or package_qty is null, 1, package_qty))*if(confirm = 0 and bara = 0, 1, confirm) as actual_loading from detail_checksheets
     ) as stuffings
     group by id_checkSheet
     ) as actual_stuffing
     on actual_stuffing.id_checkSheet = master_checksheets.id_checkSheet
     where master_checksheets.deleted_at is null and master_checksheets.Stuffing_date = '".$st_date."'
     order by field(stats, 'LOADING', 'INSPECTION', '-', 'DEPARTED')";

     $stuffings = db::select($query);

     if($stuffings != null){
          Mail::to($mail_to)->send(new SendEmail($stuffings, 'stuffing'));
     }
}

public function delete($id){
     $time = MasterChecksheet::find($id);

     $master = MasterChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
     ->delete();
     $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
     ->delete();

     $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
     ->delete();


     $response = array(
          'status' => true,
          'message' => 'Delete Success',
     );
     $time2 = MasterChecksheet::orderBy('created_at', 'desc')
     ->get();

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

public function deleteReimport(Request $request)
{
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

}
