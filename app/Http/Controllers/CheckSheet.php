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
      $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      $container = AreaInspection::orderBy('id','ASC')
      ->get();
      $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
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
     $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
     ->get();
     $container = AreaInspection::orderBy('id','ASC')
     ->get();
     $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
     ->get();
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
      $detail = DetailChecksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      $container = AreaInspection::orderBy('id','ASC')
      ->get();
      $Inspection = Inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
   
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
    $masterid = MasterChecksheet::orderBy('id','desc')
    ->first();
    $masterid_fix = $masterid->id;
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
        'created_by' => $id
      ]);

      $Inspection = new Inspection([
        'id_checksheet' => $code_generator->prefix . $number1,
        'created_by' => $id
      ]);
      $Inspection->save();
      $master->save();

      foreach ($rows as $row)
      {
        if (strlen($row) > 0) {
          $row = explode("\t", $row);
          if ($row[0] != ''){
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
            ]);
            $detail->save();
            $code_generator->index = $code_generator->index+1;
            $code_generator->save();
            

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
    if($request->hasFile('check_sheet_import')){
            // st_assemblies::truncate();

      $id = Auth::id();

      $file = $request->file('check_sheet_import');
      $data = file_get_contents($file);
      $code_master = $request->get('master_id');
      $rows = explode("\r\n", $data);
      foreach ($rows as $row)
      {
        if (strlen($row) > 0) {
          $row1 = explode("\t", $row);
          $detail = new DetailChecksheet([
            'id_checkSheet' =>$code_master,
            'destination' => $row1[0],
            'invoice' => $row1[1],
                // 'countainer_number' => $row1[2],
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
      return redirect('/index/CheckSheet')->with('status', 'New Check Sheet has been imported.')->with('page', 'Check Sheet');

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
    $response = array(
      'status' => true,
      'message' => 'Update Success',
    );

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
    $Inspection->created_by = $id_user;
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
    $response = array(
      'status' => true,
      'message' => 'Update Success',
    );

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
    $master->etd_sub = $request->get('etd_subE');
    $master->carier = $request->get('carierE');
    $master->payment = $request->get('paymentE');
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
    $master->status = $request->get('status');
    $master->check_by = $id_user;
    $master->save();


    return redirect('/index/CheckSheet')->with('status', 'Check Sheet has been saved.')->with('page', 'Check Sheet');

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


}
