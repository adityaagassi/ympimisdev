<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\st_assemblies;
use App\OriginGroup;
use App\CodeGenerator;
use App\master_checksheet;
use App\detail_checksheet;
use App\inspection;
use App\ShipmentCondition;
use App\area_inspection;
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

      $time = master_checksheet::orderBy('created_at', 'desc')
      ->get();

      return view('Check_Sheet.index', array(
        'time' => $time
      ))->with('page', 'Check Sheet');
        //
    }

    public function show($id)
    {
      
      $time = master_checksheet::find($id);

      $detail = detail_checksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
       $container = area_inspection::orderBy('id','ASC')
      ->get();
      $inspection = inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      
      return view('Check_Sheet.show', array(
        'time' => $time,
        'detail' => $detail,
        'container' => $container,
        'inspection' => $inspection,
      ))->with('page', 'Check Sheet');
        //
    }

    public function check($id)
    {
      // $get = $request->get('stamp_number_reprint');
      $time = master_checksheet::find($id);
      $detail = detail_checksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      $container = area_inspection::orderBy('id','ASC')
      ->get();
      $inspection = inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      return view('Check_Sheet.check', array(
        'time' => $time,
        'detail' => $detail,
        'container' => $container,
        'inspection' => $inspection,
      ))->with('page', 'Check Sheet');
        //
    }
public function print_check($id)
    {

     $time = master_checksheet::find($id);
      $detail = detail_checksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      $container = area_inspection::orderBy('id','ASC')
      ->get();
      $inspection = inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      return view('Check_Sheet.print', array(
        'time' => $time,
        'detail' => $detail,
        'container' => $container,
        'inspection' => $inspection,
      ))->with('page', 'Check Sheet');
        //
    }

    public function print_check_surat($id)
    {

     $time = master_checksheet::find($id);
      $detail = detail_checksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      $container = area_inspection::orderBy('id','ASC')
      ->get();
      $inspection = inspection::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      return view('Check_Sheet.printsurat', array(
        'time' => $time,
        'detail' => $detail,
        'container' => $container,
        'inspection' => $inspection,
      ))->with('page', 'Check Sheet');
        //
    }

    public function checkmarking($id)
    {
      // $get = $request->get('stamp_number_reprint');
      $time = master_checksheet::find($id);
      $detail = detail_checksheet::where('id_checkSheet','=' ,$time->id_checkSheet)
      ->get();
      $container = area_inspection::orderBy('id','ASC')
      ->get();
      return view('Check_Sheet.checkmarking', array(
        'time' => $time,
        'detail' => $detail,
        'container' => $container,
      ))->with('page', 'Check Sheet');
        //
    }

    public function import(Request $request)
    {
      $masterid = master_checksheet::orderBy('id','desc')
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
        foreach ($rows as $row)
        {
          if (strlen($row) > 0) {
            $a++;
            
            $row = explode("\t", $row);
            if ($row[0] != ''){
              $code = $number+$a;
              $number1 = sprintf("%'.0" . $code_generator->length . "d" , $code);
              $master = new master_checksheet([
                'id_input' => $row[0],
                'id_checkSheet' =>$code_generator->prefix . $number1,
                'destination' => $row[1],
                'invoice' => $row[2],
                'countainer_number' => $row[3],
                'seal_number' => $row[4],
                'shipped_from' => $row[5],
                'shipped_to' => $row[6],
                'carier' => $row[7],
                'payment' => $row[8],
                'etd_sub' => $row[9],
                'Stuffing_date' => $row[10],
                'created_by' => $id
              ]);
              
              $inspection = new inspection([
              'id_checksheet' => $code_generator->prefix . $number1,
              'created_by' => $id
             ]);

              $inspection->save();
              $master->save();
              $code_generator->index = $code_generator->index+1;
              $code_generator->save();
            
            }
          
          }
        }

        foreach ($rows as $row1)
        {
          if (strlen($row1) > 0) {
            $a++;
            
            $row1 = explode("\t", $row1);
            if ($row1[0] != ''){
              $code = $number+$a;
              // $date1 = master_checksheet::select(DB::raw('ADDTIME(created_at,-10)'))
              // ->orderBy('created_at','desc')
              // ->first();
              $master1 = master_checksheet::where('id_input','=',$row1[11])
              ->where('id','>' ,$masterid_fix)
              ->first();
              $code_master = $master1->id_checkSheet;
              $detail = new detail_checksheet([
                'id_checkSheet' =>$code_master,
                'destination' => $row1[12],
                'invoice' => $row1[13],
                // 'countainer_number' => $row1[14],
                'gmc' => $row1[14],
                'goods' => $row1[15],
                'marking' => $row1[16],
                'package_qty' => $row1[17],
                'package_set' => $row1[18],
                'qty_qty' => $row1[19],
                'qty_set' => $row1[20],
                'created_by' => $id
              ]);
              $detail->save();
            }
            if ($row1[0] == ''){
              //   $date1 = master_checksheet::select(DB::raw('ADDTIME(created_at,-10)'))
              // ->orderBy('created_at','desc')
              // ->first();
              $master1 = master_checksheet::where('id_input','=',$row1[11])
              ->where('id','>' ,$masterid_fix)
              ->first();
              $code_master = $master1->id_checkSheet;
              $detail = new detail_checksheet([
                'id_checkSheet' =>$code_master,
                'destination' => $row1[12],
                'invoice' => $row1[13],
                // 'countainer_number' => $row1[14],
                 'gmc' => $row1[14],
                'goods' => $row1[15],
                'marking' => $row1[16],
                'package_qty' => $row1[17],
                'package_set' => $row1[18],
                'qty_qty' => $row1[19],
                'qty_set' => $row1[20],
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
               $detail = new detail_checksheet([
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
      $detail_checksheet = detail_checksheet::find($request->get('id_detail'));
      $detail_checksheet->confirm = $request->get('confirm');
      $detail_checksheet->diff = $request->get('diff');
      $detail_checksheet->save();
      $response = array(
        'status' => true,
        'message' => 'Update Success',
      );

    }

    public function add(Request $request){
      $id_user = Auth::id();
      $inspection = inspection::where('id_checksheet','=', $request->get('id')) 
      ->select('id_checksheet')
      ->first();
      if ($inspection == '' ){
        $inspection1 = new inspection([
          'id_checksheet' => $request->get('id'),
          'created_by' => $id_user
        ]);
        $inspection1->save();
      }
     
    }

    
    public function addDetail(Request $request){
      $id_user = Auth::id();
      $inspection = inspection::where('id_checksheet','=', $request->get('id'))       
      ->first();
      $a = $request->get('inspection');
      $inspection->$a = $request->get('confirm');
      $inspection->created_by = $id_user;
      $inspection->save();
      $response = array(
        'status' => true,
        'message' => 'Update Success',
      );

    }

    public function addDetail2(Request $request){
      $id_user = Auth::id();
      $inspection = inspection::where('id_checksheet','=', $request->get('id'))       
      ->first();
      $a = $request->get('remark');
      $inspection->$a = $request->get('text');
      $inspection->created_by = $id_user;
      $inspection->save();
      $response = array(
        'status' => true,
        'message' => 'Update Success',
      );

    }

    public function save(Request $request){
      $id_user = Auth::id();
      $master = master_checksheet::where('id_checksheet','=', $request->get('id'))       
      ->first();
      $master->status = $request->get('status');
      $master->check_by = $id_user;
      $master->save();
     

     return redirect('/index/CheckSheet')->with('status', 'Check Sheet has been saved.')->with('page', 'Check Sheet');
      
    }

    
  }
