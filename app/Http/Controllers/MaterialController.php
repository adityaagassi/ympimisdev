<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Material;
use DataTables;
use Response;
use App\OriginGroup;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use File;
use App\StorageLocation;
use App\ReturnAdditional;
use App\MaterialControl;
use App\MaterialStockPolicy;
use App\MaterialRequirementPlan;
use App\MaterialPlanDelivery;
use App\MaterialInOut;
use Carbon\Carbon;

class MaterialController extends Controller
{
     private $category;
     private $hpl;
     private $valcl;
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
               'SX'
          ];
          $this->category = [
               'FG',
               'KD',
               'WIP',
               'RAW'
          ];
          $this->valcl = [
               '9010',
               '9030',
               '9040',
               '9041',
          ];
     }
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
public function index()
{
     $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();

     return view('materials.index', array(
          'valcls' => $this->valcl,
          'hpls' => $this->hpl,
          'categories' => $this->category,
          'origin_groups' => $origin_groups
     ))->with('page', 'Material');

}

public function indexMaterialMonitoring($id){

     if($id == 'direct'){
          $title = 'Raw Material Monitoring (Direct)';
          $title_jp = '素材監視「」';
          $material_code = $id;
     }

     if($id == 'indirect'){
          $title = 'Raw Material Monitoring (Indirect)';
          $title_jp = '素材監視「」';
          $material_code = $id;
     }

     if($id == 'subcon'){
          $title = 'Raw Material Monitoring (Subcon)';
          $title_jp = '素材監視「」';
          $material_code = $id;
     }


     return view('materials.material_monitoring', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'material_code' => $material_code
     ))->with('page', 'Raw Material Monitoring')->with('Head', 'Raw Material Monitoring'); 
}

public function uploadMaterialMonitoring(Request $request){
     $id = $request->get('id');
     $upload = $request->get('upload');
     $error_count = array();
     $ok_count = array();

     $uploadRows = preg_split("/\r?\n/", $upload);

     if($id == 'policy'){
          $period = date('Y-m-01', strtotime($request->get('period')));
          $delete = MaterialStockPolicy::where('period', '=', $period)->forceDelete();
     }

     if($id == 'usage'){
          $period = date('Y-m-01', strtotime($request->get('period')));
          $delete = MaterialStockPolicy::where('period', '=', $period)->forceDelete();
     }

     if($id == 'delivery'){
          $period_from = date('Y-m-01', strtotime($request->get('period')));
          $period_to = date('Y-m-t', strtotime($request->get('period')));
          $delete = MaterialPlanDelivery::where('due_date', '>=', $period_from)
          ->where('due_date', '<=', $period_to)
          ->forceDelete();
     }

     if($id == 'inout'){
          $period_from = date('Y-m-d', strtotime($request->get('inoutFrom')));
          $period_to = date('Y-m-d', strtotime($request->get('inoutTo')));
          $delete = MaterialPlanDelivery::where('entry_date', '>=', $period_from)
          ->where('entry_date', '<=', $period_to)
          ->forceDelete();
     }

     foreach($uploadRows as $uploadRow){

          $uploadColumn = preg_split("/\t/", $uploadRow);

          if($id == 'material'){
               $material = $uploadColumn[0];
               $description = $uploadColumn[1];
               $vendor_code = $uploadColumn[2];
               $vendor_name = $uploadColumn[3];
               $category = $uploadColumn[4];
               $pic = $uploadColumn[5];
               $remark = $uploadColumn[6];

               if(strlen($material) < 7){
                    array_push($error_count, 'GMC Unmatch '.$material.' ('.strlen($material).')');
               }
               else if(strlen($vendor_code) < 4){
                    array_push($error_count, 'Vendor Code Unmatch '.$vendor_code.' ('.strlen($vendor_code).')');             
               }
               else if($category != 'LOKAL' && $category != 'IMPORT'){
                    array_push($error_count, 'Category Code Unmatch '.$category.' ('.strlen($vendor_code).')'); 
               }
               else if($material == "" || $description == "" || $vendor_code == "" || $vendor_name == "" || $category == "" || $pic == "" || $remark == ""){
                    array_push($error_count, 'Data Blank '.$material); 
               }
               else{
                    try{
                         $material_control = MaterialControl::updateOrCreate(
                              ['material_number' => $material],
                              ['material_description' => $description, 'vendor_code' => $vendor_code, 'vendor_name' => $vendor_name, 'category' => $category, 'pic' => $pic, 'remark' => $remark, 'created_by' => Auth::id(), 'updated_at' => date('Y-m-d H:i:s')]
                         );
                         $material_control->save();

                         array_push($ok_count, 'ok');
                    }
                    catch (Exception $e) {
                         array_push($error_count, $e->getMessage());
                    }    
               }
          }

          if($id == 'policy'){
               $material = $uploadColumn[0];
               $description = $uploadColumn[1];
               $policy = $uploadColumn[2];

               if(strlen($material) < 7 || strlen($material) > 8){
                    array_push($error_count, 'GMC Unmatch '.$material.' ('.strlen($material).')');
               }
               else if($period == "" || $material == "" || $description == "" || $policy == ""){
                    array_push($error_count, 'Data Blank '.$material); 
               }
               else if(preg_match("/[a-z]/i", $policy)){
                    array_push($error_count, 'Data not number '.$material);                    
               }
               else{
                    try{
                         $material_stock_policy = new MaterialStockPolicy([
                              'period' => $period,
                              'material_number' => $material,
                              'material_description' => $description,
                              'policy' => $policy,
                              'created_by' => Auth::id()
                         ]);
                         $material_stock_policy->save();

                         array_push($ok_count, 'ok');
                    }
                    catch (Exception $e) {
                         array_push($error_count, $e->getMessage());
                    }
               }
          }

          if($id == 'usage'){
               $material = $uploadColumn[0];
               $due_date = Carbon::createFromFormat('d/m/Y', $uploadColumn[1])->format('Y-m-d');
               $usage = $uploadColumn[2];
               // if(!$uploadColumn[3]){
                    $remark = "";
               // }
               // else{
               //      $remark = $uploadColumn[3];
               // }

               if(strlen($material) < 7 || strlen($material) > 8){
                    array_push($error_count, 'GMC Unmatch '.$material.' ('.strlen($material).')');
               }
               else if($due_date == "" || $material == "" || $usage == ""){
                    array_push($error_count, 'Data Blank '.$material); 
               }
               else if(date('Y-m', strtotime($due_date)) != date('Y-m', strtotime($request->get('period')))){
                    array_push($error_count, 'Period Unmatch '.$material.' '.date('Y-m', strtotime($due_date)).' '.date('Y-m', strtotime($request->get('period'))));                     
               }
               else if(preg_match("/[a-z]/i", $usage)){
                    array_push($error_count, 'Data not number '.$material.' '.$due_date.' '.$usage);                    
               }
               else{
                    try{
                         $material_requirement_plan = new MaterialRequirementPlan([
                              'material_number' => $material,
                              'due_date' => $due_date,
                              'usage' => $usage,
                              'remark' => $remark,
                              'created_by' => Auth::id()
                         ]);
                         $material_requirement_plan->save();

                         array_push($ok_count, 'ok');
                    }
                    catch (Exception $e) {
                         array_push($error_count, $e->getMessage());
                    }
               }
          }

          if($id == 'delivery'){
               $material = $uploadColumn[0];
               $due_date = Carbon::createFromFormat('d/m/Y', $uploadColumn[1])->format('Y-m-d');
               // $due_date = $uploadColumn[1];
               $quantity = $uploadColumn[2];
               $remark = "";

               if(strlen($material) < 7 || strlen($material) > 8){
                    array_push($error_count, 'GMC Unmatch '.$material.' ('.strlen($material).')');
               }
               else if($due_date == "" || $material == "" || $remark == ""){
                    array_push($error_count, 'Data Blank '.$material); 
               }
               else if(date('Y-m', strtotime($due_date)) != $request->get('period')){
                    array_push($error_count, 'Period Unmatch '.$material.' '.$due_date);                     
               }
               else if(preg_match("/[a-z]/i", $quantity)){
                    array_push($error_count, 'Data not number '.$material.' '.$due_date.' '.$quantity);                    
               }
               else{
                    try{
                         $material_plan_delivery = new MaterialPlanDelivery([
                              'material_number' => $material,
                              'due_date' => $due_date,
                              'quantity' => $quantity,
                              'remark' => $remark,
                              'created_by' => Auth::id()
                         ]);
                         $material_plan_delivery->save();

                         array_push($ok_count, 'ok');
                    }
                    catch (Exception $e) {
                         array_push($error_count, $e->getMessage());
                    }
               }
          }

          if($id == 'inout'){
               $material = $uploadColumn[0];
               $movement_type = $uploadColumn[1];
               $issue_location = $uploadColumn[2];
               $receive_location = $uploadColumn[3];
               $quantity = $uploadColumn[4];
               $entry_date = Carbon::createFromFormat('d/m/Y', $uploadColumn[5])->format('Y-m-d');
               // $entry_date = $uploadColumn[5];
               $posting_date = Carbon::createFromFormat('d/m/Y', $uploadColumn[6])->format('Y-m-d');
               // $posting_date = $uploadColumn[6];

               if(strlen($material) < 7 || strlen($material) > 8){
                    array_push($error_count, 'GMC Unmatch '.$material.' ('.strlen($material).')');
               }
               else if(strlen($movement_type) != 3){
                    array_push($error_count, 'MvT Unmatch '.$material.' ('.strlen($material).')');
               }
               else if(strlen($issue_location) < 3 || strlen($issue_location) > 4){
                    array_push($error_count, 'Location Unmatch '.$material.' '.$issue_location.' '.$receive_location.' ('.strlen($material).')');
               }
               else if($array_push == "" || $material == "" || $issue_location == "" || $receive_location == "" || $quantity == "" || $entry_date == "" || $posting_date == ""){
                    array_push($error_count, 'Data Blank '.$material); 
               }
               // else if(date('Y-m', strtotime($posting_date)) != $request->get('period')){
               //      array_push($error_count, 'Period Unmatch '.$material.' '.$posting_date);                     
               // }
               else if(preg_match("/[a-z]/i", $quantity)){
                    array_push($error_count, 'Data not number '.$material.' '.$posting_date.' '.$quantity);                    
               }
               else{
                    try{
                         $material_in_out = new MaterialInOut([
                              'material_number' => $material,
                              'movement_type' => $movement_type,
                              'issue_location' => $issue_location,
                              'receive_location' => $receive_location,
                              'quantity' => $quantity,
                              'entry_date' => $entry_date,
                              'posting_date' => $posting_date,
                              'created_by' => Auth::id()
                         ]);
                         $material_in_out->save();

                         array_push($ok_count, 'ok');
                    }
                    catch (Exception $e) {
                         array_push($error_count, $e->getMessage());
                    }
               }
          }

     }

     $response = array(
          'status' => true,
          'id' => $id,
          'error_count' => $error_count,
          'ok_count' => $ok_count,
          'message' => 'ERROR: '.count($error_count). ' OK: '.count($ok_count)
     );
     return Response::json($response);

}

public function fetchMaterialControl(Request $request){
     $material_control = MaterialControl::orderBy('material_number', 'asc')->get();

     $response = array(
          'status' => true,
          'material_control' => $material_control,
     );
     return Response::json($response);
}


public function fetchMaterialMonitoring(Request $request){
     $period = date('Y-m-d');
     // $period = '2020-12-18';

     if(strlen($request->get('period'))>0){
          $period = $request->get('period');
     }
     $generates =  self::generateMaterialMonitoring($period);

     $material_percentages = array();
     $results1 = array();
     $count_item = 0;

     foreach ($generates['policies'] as $policy) {
          if($policy->percentage < 0.75){
               array_push($material_percentages, [
                    'material_number' => $policy->material_number,
                    'material_description' => $policy->material_description,
                    'stock' => $policy->stock,
                    'policy' => $policy->policy,
                    'percentage' => round($policy->percentage*100, 2)
               ]);
               $count_item++;
          }
     }

     $categories = array();
     $count_now = 0;

     foreach ($generates['materials'] as $material){
          $stock_mstk = 0;
          $stock_wip = 0;
          $stock_total = 0;
          $usage = 0;
          $delivery_quantity = 0;
          $actual_usage = 0;
          $actual_delivery = 0;

          if(!in_array(date('D, d M y', strtotime($material->due_date)), $categories)){
               array_push($categories, date('D, d M y', strtotime($material->due_date)));

               if(date('D, d M y', strtotime($period)) == date('D, d M y', strtotime($material->due_date))){
                    $count_now = count($categories);
               }
          }

          foreach($generates['stocks'] as $stock){

               if($material->material_number == $stock->material_number && $material->due_date == $stock->stock_date){
                    $stock_mstk = $stock->stock_mstk;
                    $stock_wip = $stock->stock_wip;
                    $stock_total = $stock->stock_total;
               }

          }

          foreach($generates['mrps'] as $mrp){
               if($material->material_number == $mrp->material_number && $material->due_date == $mrp->due_date){
                    $usage = $mrp->usage;
               }
          }

          foreach($generates['deliveries'] as $delivery){
               if($material->material_number == $delivery->material_number && $material->due_date == $delivery->due_date){
                    $delivery_quantity = $delivery->quantity;
               }
          }

          foreach($generates['material_ins'] as $material_in){
               if($material->material_number == $material_in->material_number && $material->due_date == $material_in->posting_date){
                    $actual_delivery = $material_in->quantity;
               }
          }

          foreach($generates['material_outs'] as $material_out){
               if($material->material_number == $material_out->material_number && $material->due_date == $material_out->posting_date){
                    $actual_usage = $material_out->quantity;
               }
          }

          array_push($results1, [
               'material_number' => $material->material_number,
               'material_description' => $material->material_description,
               'vendor_code' => $material->vendor_code,
               'vendor_name' => $material->vendor_name,
               'category' => $material->category,
               'pic' => $material->pic,
               'remark' => $material->remark,
               'due_date' => $material->due_date,
               'stock_mstk' => $stock_mstk,
               'stock_wip' => $stock_wip,
               'stock_total' => $stock_total,
               'plan_delivery' => $delivery_quantity,
               'actual_delivery' => $actual_delivery,
               'plan_usage' => $usage,
               'actual_usage' => $actual_usage
          ]);
     }

     $results2 = array();

     for($i = 0; $i < count($results1); $i++){
          $plan_stock = 0;

          if($results1[$i]['due_date'] > $period){
               // $plan_stock = $results2[count($results2)-1]['stock_total']+$results2[count($results2)-1]['actual_delivery']-$results2[count($results2)-1]['plan_usage'];
               // if($plan_stock != 0){
               $del = 0;
               if($results2[count($results2)-1]['plan_delivery'] == 0){
                    $del = $results2[count($results2)-1]['actual_delivery'];
               }
               else{
                    $del = $results2[count($results2)-1]['plan_delivery'];
               }
               $plan_stock = $results2[count($results2)-1]['stock_total']+$results2[count($results2)-1]['plan_stock']+$del-$results2[count($results2)-1]['plan_usage'];
               // }
          }

          array_push($results2, [
               'material_number' => $results1[$i]['material_number'],
               'material_description' => $results1[$i]['material_description'],
               'vendor_code' => $results1[$i]['vendor_code'],
               'vendor_name' => $results1[$i]['vendor_name'],
               'category' => $results1[$i]['category'],
               'pic' => $results1[$i]['pic'],
               'remark' => $results1[$i]['remark'],
               'due_date' => $results1[$i]['due_date'],
               'stock_mstk' => $results1[$i]['stock_mstk'],
               'stock_wip' => $results1[$i]['stock_wip'],
               'stock_total' => $results1[$i]['stock_total'],
               'plan_delivery' => $results1[$i]['plan_delivery'],
               'actual_delivery' => $results1[$i]['actual_delivery'],
               'plan_usage' => $results1[$i]['plan_usage'],
               'actual_usage' => $results1[$i]['actual_usage'],
               'plan_stock' => $plan_stock
          ]);
     }

     $response = array(
          'status' => true,
          'count_now' => $count_now,
          'period' => date('d M y', strtotime($period)),
          'categories' => $categories,
          'material_percentages' => $material_percentages,
          'results' => $results2,
          'count_item' => $count_item
     );
     return Response::json($response);
}

function generateMaterialMonitoring($due_date){

     // $due_date = '2021-03-05';

     $period = date('Y-m', strtotime($due_date));

     $first = date('Y-m-01', strtotime($due_date));
     $last = date('Y-m-t', strtotime($due_date));

     $policies = db::select("SELECT
          msp.period,
          msp.material_number,
          msp.material_description,
          '".$due_date."' AS stock_date,
          COALESCE ( s.stock_total, 0 ) AS stock,
          msp.policy,
          COALESCE ( s.stock_total, 0 ) / msp.policy AS percentage 
          FROM
          material_stock_policies AS msp
          LEFT JOIN (
          SELECT
          sls.material_number,
          sls.stock_date,
          sum(
          IF
          ( sl.category = 'MSTK', sls.unrestricted, 0 )) AS stock_mstk,
          sum(
          IF
          ( sl.category = 'WIP', sls.unrestricted, 0 )) AS stock_wip,
          sum( sls.unrestricted ) AS stock_total 
          FROM
          storage_location_stocks AS sls
          LEFT JOIN storage_locations AS sl ON sls.storage_location = sl.storage_location 
          WHERE
          sls.stock_date = '".$due_date."' 
          AND sls.material_number IN ( SELECT material_number FROM material_controls WHERE deleted_at IS NULL ) 
          GROUP BY
          sls.material_number,
          sls.stock_date 
          ORDER BY
          sls.material_number ASC,
          sls.stock_date ASC 
          ) AS s ON s.material_number = msp.material_number 
          WHERE
          msp.policy > 0 
          AND msp.material_number in (SELECT material_number FROM material_controls)
          AND date_format( msp.period, '%Y-%m' ) = '".$period."'
          ORDER BY
          percentage ASC");

     $material_numbers = array();

     foreach($policies as $policy){
          if($policy->percentage < 0.75){
               if(!in_array($policy->material_number, $material_numbers)){
                    array_push($material_numbers, $policy->material_number);
               }
          }
     }

     $where_materials = "";

     $material_number = "";

     for($x = 0; $x < count($material_numbers); $x++) {
          $material_number = $material_number."'".$material_numbers[$x]."'";
          if($x != count($material_numbers)-1){
               $material_number = $material_number.",";
          }
     }
     $where_materials = $material_number;

     if($where_materials == ""){
          $where_materials = "''";
     }

     $materials = db::select("SELECT
          mc.material_number,
          wc.week_date AS due_date,
          mc.material_description,
          mc.vendor_code,
          mc.vendor_name,
          mc.category,
          mc.pic,
          mc.remark 
          FROM
          weekly_calendars AS wc
          CROSS JOIN material_controls AS mc 
          WHERE
          mc.deleted_at IS NULL 
          AND date_format( wc.week_date, '%Y-%m' ) = '".$period."' 
          AND mc.material_number in (".$where_materials.")
          AND wc.remark <> 'H'
          ORDER BY
          mc.material_number ASC,
          wc.week_date ASC");

     $stocks = db::select("SELECT
          sls.material_number,
          sls.stock_date,
          sum(
          IF
          ( sl.category = 'MSTK', sls.unrestricted, 0 )) AS stock_mstk,
          sum(
          IF
          ( sl.category = 'WIP', sls.unrestricted, 0 )) AS stock_wip,
          sum( sls.unrestricted ) AS stock_total 
          FROM
          storage_location_stocks AS sls
          LEFT JOIN storage_locations AS sl ON sls.storage_location = sl.storage_location 
          WHERE
          date( sls.stock_date ) >= '".$first."' 
          AND date( sls.stock_date ) <= '".$due_date."'
          AND sls.material_number IN (".$where_materials.") 
          AND sl.category IN ('MSTK', 'WIP')
          GROUP BY
          sls.material_number,
          sls.stock_date 
          ORDER BY
          sls.material_number ASC,
          sls.stock_date ASC");

     $mrps = db::select("SELECT
          mrp.material_number,
          mrp.due_date,
          mrp.usage 
          FROM
          material_requirement_plans AS mrp 
          WHERE
          date_format( mrp.due_date, '%Y-%m' ) = '".$period."'
          AND mrp.material_number IN (".$where_materials.")");

     $deliveries = db::select("SELECT
          mpd.material_number,
          mpd.due_date,
          mpd.quantity 
          FROM
          material_plan_deliveries AS mpd 
          WHERE
          date_format( mpd.due_date, '%Y-%m' ) = '".$period."'
          AND mpd.material_number IN (".$where_materials.")");

     $material_ins = db::select("SELECT
          mio.posting_date,
          mio.material_number,
          sum( mio.quantity ) AS quantity 
          FROM
          material_in_outs AS mio 
          WHERE
          mio.issue_location = 'MSTK' 
          AND mio.movement_type IN ( '101', '102', '9T3', '9T4' )
          AND date( mio.posting_date) >= '".$first."' 
          AND date( mio.posting_date) < '".$due_date."'
          AND material_number IN (".$where_materials.") 
          GROUP BY
          mio.posting_date,
          mio.material_number");

     $material_outs = db::select("SELECT
          mio.posting_date,
          mio.material_number,
          sum( mio.quantity ) AS quantity 
          FROM
          material_in_outs AS mio 
          WHERE
          mio.issue_location = 'MSTK' 
          AND mio.movement_type IN ( '9I3', '9I4' ) 
          AND date( mio.posting_date) >= '".$first."' 
          AND date( mio.posting_date) < '".$due_date."'
          AND material_number IN (".$where_materials.") 
          GROUP BY
          mio.posting_date,
          mio.material_number");

     return array(
          'policies' => $policies,
          'material_numbers' => $material_numbers,
          'materials' => $materials,
          'stocks' => $stocks,
          'mrps' => $mrps,
          'deliveries' => $deliveries,
          'material_ins' => $material_ins,
          'material_outs' => $material_outs
     );

}

public function indexMaterialRequest(){
     $title = 'Material Request';
     $title_jp = '??';
     $storage_locations = StorageLocation::select('location', 'storage_location')->distinct()
     ->orderBy('location', 'asc')
     ->get();

     return view('materials.request.request', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'storage_locations' => $storage_locations,
     ))->with('page', 'Material Request')->with('Head', 'Material Delivery');     
}

public function indexMaterialReceive(){

}

public function indexMaterialDelivery(){

}

public function fetchMaterialRequestList(Request $request){
     $lists = ReturnAdditional::select('material_number', 'description', 'issue_location', 'receive_location', 'lot')
     ->where('receive_location', '=', $request->get('location'))
     ->orderBy('issue_location', 'asc')
     ->orderBy('material_number', 'asc')
     ->distinct()
     ->get();

     if(count($lists) == 0){
          $response = array(
               'status' => false,
               'materials' => "Tidak ada material untuk lokasi tersebut."
          );
          return Response::json($response);
     }

     $response = array(
          'status' => true,
          'materials' => "Lokasi berhasil dipilih.",
          'lists' => $lists,
     );
     return Response::json($response);
}

public function fetchMaterial()
{
     $materials = Material::leftJoin("origin_groups","origin_groups.origin_group_code","=","materials.origin_group_code")
     ->orderBy('material_number', 'ASC')
     ->select("materials.id","materials.material_number","materials.material_description","materials.base_unit","materials.issue_storage_location","materials.mrpc","materials.valcl","origin_groups.origin_group_name","materials.hpl","materials.category","materials.model")
     ->get();

     return DataTables::of($materials)
     ->addColumn('action', function($materials){
          return '
          <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$materials->id.')">View</button>
          <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$materials->id.')">Edit</button>
          <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$materials->id.',\''.$materials->material_number.'\')">Delete</button>';
     })

     ->rawColumns(['action' => 'action'])
     ->make(true);
}

/**
* Store a newly created resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @return \Illuminate\Http\Response
*/
public function create(Request $request)
{
     try
     {
          $id = Auth::id();
          $material = new Material([
               'material_number' => $request->get('material_number'),
               'material_description' => $request->get('material_description'),
               'base_unit' => $request->get('base_unit'),
               'issue_storage_location' => $request->get('issue_storage_location'),
               'mrpc' => $request->get('mrpc'),
               'valcl' => $request->get('valcl'),
               'origin_group_code' => $request->get('origin_group_code'),
               'hpl' => $request->get('hpl'),
               'category' => $request->get('category'),
               'model' => $request->get('model'),
               'created_by' => $id
          ]);

          $material->save();

          $response = array(
               'status' => true,
               'materials' => "New Material has been created."
          );
          return Response::json($response);
     }
     catch (QueryException $e){
          $error_code = $e->errorInfo[1];
          if($error_code == 1062){
               $response = array(
                    'status' => true,
                    'materials' => "Material already exist"
               );
               return Response::json($response);
          }
          else{
               $response = array(
                    'status' => true,
                    'materials' => "Material not created."
               );
               return Response::json($response);
          }
     }

}

/**
* Display the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function view(Request $request)
{
     $query = "select mat.material_number, mat.base_unit, mat.issue_storage_location, users.`name`, material_description, origin_group_name, mat.created_at, mat.updated_at, mat.hpl, mat.category, mat.mrpc, mat.valcl from
     (select material_number, material_description, base_unit, issue_storage_location, mrpc, valcl, origin_group_code, hpl, category, created_by, created_at, updated_at from materials where id = "
     .$request->get('id').") as mat
     left join origin_groups on origin_groups.origin_group_code = mat.origin_group_code
     left join users on mat.created_by = users.id";

     $material = DB::select($query);

     $response = array(
          'status' => true,
          'datas' => $material,
     );
     return Response::json($response);
//
}

/**
* Show the form for editing the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function fetchEdit(Request $request)
{
     $hpls = $this->hpl;
     $categories = $this->category;
     $valcls = $this->valcl;
     $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();
     $material = Material::find($request->get("id"));

     $response = array(
          'status' => true,
          'datas' => $material,
     );
     return Response::json($response);
//
}

/**
* Update the specified resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function edit(Request $request)
{
     try{
          $material = Material::find($request->get("id"));
          $material->material_description = $request->get('material_description');
          $material->base_unit = $request->get('base_unit');
          $material->issue_storage_location = $request->get('issue_storage_location');
          $material->mrpc = $request->get('mrpc');
          $material->valcl = $request->get('valcl');
          $material->origin_group_code = $request->get('origin_group_code');
          $material->hpl = $request->get('hpl');
          $material->category = $request->get('category');
          $material->model = $request->get('model');
          $material->save();

          $response = array(
               'status' => true
          );
          return Response::json($response);

     }
     catch (QueryException $e){
          $error_code = $e->errorInfo[1];
          if($error_code == 1062){
               $response = array(
                    'status' => true,
                    'datas' => "Material already exist",
               );
               return Response::json($response);
          }
          else{
               $response = array(
                    'status' => true,
                    'datas' => "Update Material Error.",
               );
               return Response::json($response);
          }
     }
}

/**
* Remove the specified resource from storage.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function delete(Request $request)
{
     $material = Material::find($request->get("id"));
     $material->forceDelete();

     $response = array(
          'status' => true
     );
     return Response::json($response);
}

/**
* Import resource from Text File.
*
* @return List Transfer
*
*/
public function import(Request $request)
{
     if($request->hasFile('material')){
          Material::truncate();

          $id = Auth::id();

          $file = $request->file('material');
          $data = file_get_contents($file);

          $rows = explode("\r\n", $data);
          foreach ($rows as $row)
          {
               if (strlen($row) > 0) {
                    $row = explode("\t", $row);
                    $material_number = '';
                    if(strlen($row[0]) == 6){
                         $material_number = "0" . $row[0];
                    }
                    elseif(strlen($row[0]) == 5){
                         $material_number = "00" . $row[0];
                    }
                    else{
                         $material_number = $row[0];
                    }
                    $origin_group_code = '';
                    if(strlen($row[6]) == 2){
                         $origin_group_code = "0".$row[6];
                    }
                    else{
                         $origin_group_code = $row[6];
                    }
                    $material = new Material([
                         'material_number' => $material_number,
                         'material_description' => $row[1],
                         'base_unit' => $row[2],
                         'issue_storage_location' => $row[3],
                         'mrpc' => $row[4],
                         'valcl' => $row[5],
                         'origin_group_code' => $origin_group_code,
                         'hpl' => $row[7],
                         'category' => $row[8],
                         'model' => $row[9],
                         'created_by' => $id,
                    ]);

                    $material->save();
               }
          }
          return redirect('/index/material')->with('status', 'New materials has been imported.')->with('page', 'Material');

     }
     else
     {
          return redirect('/index/material')->with('error', 'Please select a file.')->with('page', 'Material');
     }
}
}
