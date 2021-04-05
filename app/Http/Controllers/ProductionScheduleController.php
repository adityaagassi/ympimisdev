<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Material;
use App\Destination;

use App\ProductionSchedulesOneStep;
use App\ProductionSchedulesTwoStep;
use App\ProductionSchedulesThreeStep;

use App\ProductionSchedule;
use App\ProductionForecast;
use App\ProductionRequest;

use App\FirstInventory;
use App\PsiCalendar;
use App\WeeklyCalendar;
use App\OriginGroup;
use App\FloDetail;
use DataTables;
use Response;





class ProductionScheduleController extends Controller{

	public function __construct(){
		$this->middleware('auth');
        $this->ymmj =  array(
            "Monday"=> 4,
            "Tuesday"=> 3,
            "Wednesday"=> 2,
            "Thursday"=> 5,
            "Friday"=> 4,
            "Saturday"=> 3,
            "Sunday"=> 2
        );
        $this->xy = array(
            "Tuesday"=> 6,
            "Wednesday"=> 5,
            "Thursday"=> 4,
            "Friday"=> 5,
            "Saturday"=> 4,
            "Sunday"=> 3,
            "Monday"=> 2
        );
    }
    

    public function fetchViewProductionScheduleKd(Request $request){

        $month = '';
        if(strlen($request->get('month')) > 0){
            $month = $request->get('month');
        }else{
            $month = date('Y-m');
        }

        $hpl = '';
        if($request->get('hpl') != null){
            $hpls =  $request->get('hpl');
            for ($i=0; $i < count($hpls); $i++) {
                $hpl = $hpl."'".$hpls[$i]."'";
                if($i != (count($hpls)-1)){
                    $hpl = $hpl.',';
                }
            }
            $hpl = "AND m.hpl IN (".$hpl.") ";
        }

        $dates = WeeklyCalendar::where('week_date', 'like', '%'.$month.'%')->get();

        $materials = DB::select("SELECT DISTINCT ps.material_number, m.material_description, m.hpl, v.lot_completion FROM production_schedules_one_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            LEFT JOIN material_volumes v ON v.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '".$month."'
            ".$hpl."
            ORDER BY m.hpl, ps.material_number ASC");

        $prod_schedules = DB::select("SELECT ps.due_date, ps.material_number, m.material_description, SUM(ps.quantity) AS quantity FROM production_schedules_one_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '".$month."'
            ".$hpl."
            GROUP BY ps.due_date, ps.material_number, m.material_description");


        $response = array(
            'status' => true,
            'dates' => $dates,
            'materials' => $materials,
            'prod_schedules' => $prod_schedules,
            'month' => date('F Y', strtotime($month."-01"))
        );
        return Response::json($response);
    }


    public function fetchViewGenerateProductionScheduleKd(Request $request){

        $month = '';
        if(strlen($request->get('month')) > 0){
            $month = $request->get('month');
        }else{
            $month = date('Y-m');
        }

        $hpl = '';
        if($request->get('hpl') != null){
            $hpls =  $request->get('hpl');
            for ($i=0; $i < count($hpls); $i++) {
                $hpl = $hpl."'".$hpls[$i]."'";
                if($i != (count($hpls)-1)){
                    $hpl = $hpl.',';
                }
            }
            $hpl = "AND m.hpl IN (".$hpl.") ";
        }

        $dates = WeeklyCalendar::where('week_date', 'like', '%'.$month.'%')->get();

        $materials = DB::select("SELECT DISTINCT ps.material_number, m.material_description, m.hpl, v.lot_completion FROM production_schedules_two_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            LEFT JOIN material_volumes v ON v.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '".$month."'
            ".$hpl."
            ORDER BY m.hpl, ps.material_number ASC");

        $prod_schedules = DB::select("SELECT ps.due_date, ps.material_number, m.material_description, SUM(ps.quantity) AS quantity FROM production_schedules_two_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '".$month."'
            ".$hpl."
            GROUP BY ps.due_date, ps.material_number, m.material_description");

        $sum_step_one = DB::select("SELECT ps.material_number, m.material_description, SUM(ps.quantity) AS quantity FROM production_schedules_one_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '".$month."'
            ".$hpl."
            GROUP BY ps.material_number, m.material_description");


        $response = array(
            'status' => true,
            'dates' => $dates,
            'materials' => $materials,
            'prod_schedules' => $prod_schedules,
            'sum_step_one' => $sum_step_one,
            'month' => date('F Y', strtotime($month."-01"))
        );
        return Response::json($response);
    }


    public function fetchGenerateProductionScheduleKd(Request $request){

        $month = '';
        if(strlen($request->get('month')) > 0){
            $month = $request->get('month');
        }else{
            $month = date('Y-m');
        }

        $hpl = '';
        if($request->get('hpl') != null){
            $hpls =  $request->get('hpl');
            for ($i=0; $i < count($hpls); $i++) {
                $hpl = $hpl."'".$hpls[$i]."'";
                if($i != (count($hpls)-1)){
                    $hpl = $hpl.',';
                }
            }
            $hpl = "AND m.hpl IN (".$hpl.") ";
        }


        $delete = ProductionSchedulesTwoStep::leftJoin('materials', 'materials.material_number', 'production_schedules_two_steps.material_number')
        ->where(db::raw('date_format(production_schedules_two_steps.due_date, "%Y-%m")') ,$month)
        ->whereIn('materials.hpl', $request->get('hpl'))
        ->delete();

        $materials = ProductionSchedulesOneStep::leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
        ->where(db::raw('date_format(due_date, "%Y-%m")') , $month)
        ->whereIn('materials.hpl', $request->get('hpl'))
        ->select('production_schedules_one_steps.material_number')
        ->distinct()
        ->get();

        for ($i=0; $i < count($materials); $i++) {
            $step_one = ProductionSchedulesOneStep::leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'production_schedules_one_steps.material_number')
            ->where(db::raw('date_format(due_date, "%Y-%m")') , $month)
            ->where('production_schedules_one_steps.material_number', $materials[$i]->material_number)
            ->select(
                'production_schedules_one_steps.due_date',
                'production_schedules_one_steps.material_number',
                'material_volumes.lot_completion',
                'production_schedules_one_steps.quantity'
            )
            ->orderBy('production_schedules_one_steps.due_date', 'ASC')
            ->get();


            $koef = 0;

            for ($j=0; $j < count($step_one); $j++) {

                $koef += $step_one[$j]->quantity;
                $floor = floor($koef / $step_one[$j]->lot_completion);

                if($floor > 0){

                    $insert = new ProductionSchedulesTwoStep ([
                        'due_date' => $step_one[$j]->due_date,
                        'material_number' => $step_one[$j]->material_number,
                        'quantity' => $floor * $step_one[$j]->lot_completion,
                        'created_by' => Auth::id()
                    ]);
                    $insert->save();

                    $koef = $koef % $step_one[$j]->lot_completion;
                }
            }
        }

        $response = array(
            'status' => true
        );
        return Response::json($response);
    }

    public function fetchViewGenerateShipmentScheduleKd(Request $request){

        $month = '';
        if(strlen($request->get('month')) > 0){
            $month = $request->get('month');
        }else{
            $month = date('Y-m');
        }

        $hpl = '';
        if($request->get('hpl') != null){
            $hpls =  $request->get('hpl');
            for ($i=0; $i < count($hpls); $i++) {
                $hpl = $hpl."'".$hpls[$i]."'";
                if($i != (count($hpls)-1)){
                    $hpl = $hpl.',';
                }
            }
            $hpl = "AND m.hpl IN (".$hpl.") ";
        }

        $dates = PsiCalendar::where('sales_period', 'like', '%'.$month.'%')->get();

        $materials = DB::select("SELECT DISTINCT ps.material_number, m.material_description, m.hpl, r.destination_code, d.destination_shortname FROM production_schedules_two_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            LEFT JOIN production_requests r ON r.material_number = ps.material_number
            LEFT JOIN destinations d ON d.destination_code = r.destination_code
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '".$month."'
            ".$hpl."
            ORDER BY m.hpl ASC, ps.material_number ASC, d.destination_shortname DESC");

        $shipments = DB::select("SELECT ps.st_date, ps.material_number, m.hpl, d.destination_shortname, SUM(ps.quantity) AS quantity FROM production_schedules_three_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            LEFT JOIN destinations d ON d.destination_code = ps.destination_code
            WHERE DATE_FORMAT(ps.st_month, '%Y-%m') = '".$month."'
            ".$hpl."
            GROUP BY ps.st_date, ps.material_number, m.hpl, d.destination_shortname
            ORDER BY m.hpl ASC, ps.material_number ASC, d.destination_shortname DESC");

        $requests = DB::select("SELECT r.material_number, d.destination_shortname, r.quantity FROM `production_requests` r
            LEFT JOIN destinations d ON d.destination_code = r.destination_code
            WHERE date_format(request_month, '%Y-%m') = '".$month."'");


        $response = array(
            'status' => true,
            'dates' => $dates,
            'materials' => $materials,
            'shipments' => $shipments,
            'requests' => $requests,
            'month' => date('F Y', strtotime($month."-01"))
        );
        return Response::json($response);
    }

    public function fetchGenerateShipmentScheduleKd(Request $request){
        $month = '';
        if(strlen($request->get('month')) > 0){
            $month = $request->get('month');
        }else{
            $month = date('Y-m');
        }



        $delete_step3 = ProductionSchedulesThreeStep::where('st_month', $month.'-01')->delete();

        $update_step2 = ProductionSchedulesTwoStep::where(db::raw('date_format(due_date, "%Y-%m")') , $month)
        ->update([
            'st_plan' => 0
        ]);

        $update_stock = FirstInventory::where(db::raw('date_format(stock_date, "%Y-%m")') , $month)
        ->update([
            'st_plan' => 0
        ]);

        $update_request = ProductionRequest::where('request_month', $month.'-01')
        ->update([
            'st_plan' => 0
        ]);



        $request = ProductionRequest::where(db::raw('date_format(request_month, "%Y-%m")') , $month)
        ->orderBy('material_number', 'ASC')
        ->orderBy('priority', 'ASC')
        ->get();

        $psi_start = PsiCalendar::where('sales_period', 'like', '%'.$month.'%')->orderBy('week_date', 'ASC')->first();
        
        $psi_finish = PsiCalendar::where('sales_period', 'like', '%'.$month.'%')->orderBy('week_date', 'DESC')->first();


        for ($i=0; $i < count($request); $i++) {

            $st_plan = $request[$i]->quantity;

            $productions = DB::select("SELECT 'stock' AS type, inv.stock_date AS due_date, inv.material_number, (inv.quantity - inv.st_plan) AS quantity, m.hpl FROM first_inventories inv
                LEFT JOIN materials m ON m.material_number = inv.material_number
                WHERE inv.material_number = '".$request[$i]->material_number."'
                AND DATE_FORMAT(inv.stock_date,'%Y-%m') = '".$month."' 
                AND m.category = 'KD'
                HAVING quantity > 0
                UNION
                SELECT 'plan' AS type, ps.due_date, ps.material_number, (ps.quantity - ps.st_plan) AS quantity, m.hpl FROM production_schedules_two_steps ps
                LEFT JOIN materials m ON ps.material_number = m.material_number
                WHERE ps.material_number = '".$request[$i]->material_number."'
                AND ps.due_date BETWEEN '".$psi_start->week_date."' AND '".$psi_finish->week_date."'
                HAVING quantity > 0
                ORDER BY due_date ASC");

            for ($j=0; $j < count($productions); $j++) {
                $koef;
                if($request[$i]->destination_code == 'Y31507'){
                    $koef = $this->ymmj[date('l', strtotime($productions[$j]->due_date))];
                }else if($request[$i]->destination_code == 'Y81804'){
                    $koef = $this->xy[date('l', strtotime($productions[$j]->due_date))];
                }

                $st_date = date('Y-m-d', strtotime('+'.$koef.' day', strtotime($productions[$j]->due_date)));
                $bl_date = date('Y-m-d', strtotime('+3 day', strtotime($st_date)));

                $quantity = $productions[$j]->quantity;
                $diff = $st_plan - $productions[$j]->quantity;

                if($diff < 0){
                    $quantity = $st_plan;
                }

                $shipment_schedule = ProductionSchedulesThreeStep::where('st_month', $month.'-01')
                ->where('shipment_condition_code', 'C2')
                ->where('destination_code', $request[$i]->destination_code)
                ->where('material_number', $productions[$j]->material_number)
                ->where('hpl', $productions[$j]->hpl)
                ->where('st_date', $st_date)
                ->first();


                if($shipment_schedule){
                    $shipment_schedule->quantity = $shipment_schedule->quantity + $quantity;
                    $shipment_schedule->save();
                }else{
                    $insert = new ProductionSchedulesThreeStep([
                        'st_month' => $month.'-01',
                        'sales_order' => $request[$i]->sales_order,
                        'shipment_condition_code' => 'C2',
                        'destination_code' => $request[$i]->destination_code,
                        'material_number' => $productions[$j]->material_number,
                        'hpl' => $productions[$j]->hpl,
                        'st_date' => $st_date,
                        'bl_date' => $bl_date,
                        'quantity' => $quantity,
                        'created_by' => Auth::id()
                    ]);
                    $insert->save();
                }

                if($productions[$j]->type == 'plan'){
                    $update_production = ProductionSchedulesTwoStep::where('due_date', $productions[$j]->due_date)
                    ->where('material_number', $productions[$j]->material_number)
                    ->first();

                    $update_production->st_plan = $update_production->st_plan + $quantity;
                    $update_production->save();
                }elseif($productions[$j]->type == 'stock'){
                    $inv = FirstInventory::where(db::raw('date_format(stock_date, "%Y-%m")') , $month)
                    ->where('material_number', $productions[$j]->material_number)
                    ->orderBy('stock_date')
                    ->first();

                    $inv->st_plan = $inv->st_plan + $quantity;
                    $inv->save();
                }



                $update_request = ProductionRequest::where('request_month', $month.'-01')
                ->where('material_number', $productions[$j]->material_number)
                ->where('destination_code', $request[$i]->destination_code)
                ->first();

                $update_request->st_plan = $update_request->st_plan + $quantity;
                $update_request->save();

                $st_plan = $st_plan - $quantity;
                if($st_plan == 0){
                    break;
                }                
            }
        }

        $response = array(
            'status' => true
        );
        return Response::json($response);

    }












    public function indexGenerateSchedule(){
        return view('production_schedules.generate_schedule');
    }

    public function generateScheduleStepOne(){

        $dates = WeeklyCalendar::where('week_date', 'like', '%2021-01%')
        ->where('remark', '<>', 'H')
        ->get();

        $forecasts = ProductionForecast::where(db::raw('date_format(forecast_month, "%Y-%m")') ,'2021-01')
        ->where('quantity', '>', 0)
        ->get();

        $delete_temps = DB::table('production_schedules_one_steps')
        ->where(db::raw('date_format(due_date, "%Y-%m")') ,'2021-01')
        ->delete();

        for ($i=0; $i < count($forecasts); $i++) {
            if($forecasts[$i]->quantity > 0){
                $material_number = $forecasts[$i]->material_number;

                $capacity = DB::table('production_capacities')
                ->where('base_model', $material_number)
                ->where('remark', 'lot')
                ->first();

                if($capacity){
                    $lot_round = floor($forecasts[$i]->quantity / $capacity->quantity);
                    $lot_mod = $forecasts[$i]->quantity % $capacity->quantity;

                    $up = ceil($lot_round / count($dates));
                    $down = floor($lot_round / count($dates));
                    $mod = $lot_round % count($dates);

                    for ($j=0; $j < count($dates); $j++) {
                        $quantity = 0;
                        if($j < $mod){
                            $quantity = $up * $capacity->quantity;
                        }else{
                            $quantity = $down * $capacity->quantity;
                        }

                        if($quantity > 0){
                            $insert = DB::table('production_schedules_one_steps')->insert([
                                'due_date' => $dates[$j]->week_date,
                                'material_number' => $material_number,
                                'quantity' => $quantity,
                                'created_by' => Auth::id()
                            ]);
                        }
                    }


                }else{
                    $rounded = floor($forecasts[$i]->quantity / count($dates));
                    $mod = $forecasts[$i]->quantity % count($dates);

                    for ($j=0; $j < count($dates); $j++) {
                        $quantity = 0;
                        if($j < $mod){
                            $quantity = $rounded + 1;
                        }else{
                            $quantity = $rounded;
                        }

                        if($quantity){
                            $insert = DB::table('production_schedules_one_steps')->insert([
                                'due_date' => $dates[$j]->week_date,
                                'material_number' => $material_number,
                                'quantity' => $quantity,
                                'created_by' => Auth::id()
                            ]);
                        }
                    }
                }  
            }          
        }
    }

    public function generateScheduleStepTwo(){

        $step_one = DB::table('production_schedules_one_steps')
        ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
        ->where('materials.hpl', 'CLFG')
        ->where(db::raw('date_format(due_date, "%Y-%m")') ,'2021-01')
        ->select(
            'production_schedules_one_steps.due_date',
            'production_schedules_one_steps.material_number',
            'production_schedules_one_steps.quantity'
        )
        ->get();

        $delete_temps = DB::table('production_schedules_two_steps')
        ->where(db::raw('date_format(due_date, "%Y-%m")') ,'2021-01')
        ->delete();

        $total_request = 0;

        for ($i=0; $i < count($step_one); $i++) { 
            $insert = DB::table('production_schedules_two_steps')->insert([
                'due_date' => $step_one[$i]->due_date,
                'material_number' => $step_one[$i]->material_number,
                'quantity' => $step_one[$i]->quantity,
                'created_by' => Auth::id()
            ]);

            $total_request += $step_one[$i]->quantity;
        }

        $dates = WeeklyCalendar::where('week_date', 'like', '%2021-01%')
        ->where('remark', '<>', 'H')
        ->get();


        $step_two_silver = DB::table('production_schedules_two_steps')
        ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
        ->where(db::raw('date_format(production_schedules_two_steps.due_date, "%Y-%m")') ,'2021-01')
        ->where('materials.base_model', 'CL SILVER')
        ->select(
            'production_schedules_two_steps.due_date',
            db::raw('SUM(production_schedules_two_steps.quantity) AS quantity')
        )
        ->groupBy('production_schedules_two_steps.due_date')
        ->get();

        //GET SILVER MAX PRODUCTION
        $silver_capacity = DB::table('production_capacities')
        ->where('base_model', 'CL SILVER')
        ->where('remark', 'capacity product')
        ->first();

        $loop = true;

        do{
            //GET SCHDULE SILVER PER HARI
            $step_two_silver = DB::table('production_schedules_two_steps')
            ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
            ->where(db::raw('date_format(production_schedules_two_steps.due_date, "%Y-%m")') ,'2021-01')
            ->where('materials.base_model', 'CL SILVER')
            ->select(
                'production_schedules_two_steps.due_date',
                db::raw('SUM(production_schedules_two_steps.quantity) AS quantity')
            )
            ->groupBy('production_schedules_two_steps.due_date')
            ->get();

            for ($i=0; $i < count($step_two_silver); $i++) {
                if($step_two_silver[$i]->quantity > $silver_capacity->quantity){

                    //SCHEDULE HARI H
                    $temp = DB::table('production_schedules_two_steps')
                    ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
                    ->where('production_schedules_two_steps.due_date', $step_two_silver[$i]->due_date)
                    ->where('materials.base_model', 'CL SILVER')
                    ->orderBy('production_schedules_two_steps.quantity', 'DESC')
                    ->get();

                    $index_date;
                    for ($j=0; $j < count($dates); $j++) { 
                        if($dates[$j]->week_date == $step_two_silver[$i]->due_date){
                            if($j == count($dates)-1){
                                $index_date = $j;
                            }else{
                                $index_date = $j+1;
                            }
                        }                        
                    }

                    $next_date = $dates[$index_date]->week_date;
                    $diff = $step_two_silver[$i]->quantity - $silver_capacity->quantity;

                    do{
                        for ($x=0; $x < count($temp); $x++) {
                            if($diff > 0){
                                $lot = DB::table('production_capacities')
                                ->where('base_model', $temp[$x]->material_number)
                                ->where('remark', 'lot')
                                ->first();

                                if($lot){
                                    $exsiting = DB::table('production_schedules_two_steps')
                                    ->where('material_number', $temp[$x]->material_number)
                                    ->where('due_date', $step_two_silver[$i]->due_date)
                                    ->first();

                                    $update_exsiting = DB::table('production_schedules_two_steps')
                                    ->where('id', $exsiting->id)
                                    ->update([
                                        'quantity' => $exsiting->quantity - $lot->quantity
                                    ]);

                                    $diff -= $lot->quantity;

                                    $next = DB::table('production_schedules_two_steps')
                                    ->where('material_number', $temp[$x]->material_number)
                                    ->where('due_date', $next_date)
                                    ->first();

                                    if($next){
                                        $update_next = DB::table('production_schedules_two_steps')
                                        ->where('id', $next->id)
                                        ->update([
                                            'quantity' => $next->quantity + $lot->quantity
                                        ]);
                                    }else{
                                        $insert = DB::table('production_schedules_two_steps')->insert([
                                            'due_date' => $next_date,
                                            'material_number' => $temp[$x]->material_number,
                                            'quantity' => $lot->quantity,
                                            'created_by' => Auth::id()
                                        ]);
                                    }                                

                                }else{
                                    $exsiting = DB::table('production_schedules_two_steps')
                                    ->where('material_number', $temp[$x]->material_number)
                                    ->where('due_date', $step_two_silver[$i]->due_date)
                                    ->first();

                                    $update_exsiting = DB::table('production_schedules_two_steps')
                                    ->where('id', $exsiting->id)
                                    ->update([
                                        'quantity' => $exsiting->quantity - 1
                                    ]);

                                    $diff--;

                                    $next = DB::table('production_schedules_two_steps')
                                    ->where('material_number', $temp[$x]->material_number)
                                    ->where('due_date', $next_date)
                                    ->first();

                                    if($next){
                                        $update_next = DB::table('production_schedules_two_steps')
                                        ->where('id', $next->id)
                                        ->update([
                                            'quantity' => $next->quantity + 1
                                        ]);
                                    }else{
                                        $insert = DB::table('production_schedules_two_steps')->insert([
                                            'due_date' => $next_date,
                                            'material_number' => $temp[$x]->material_number,
                                            'quantity' => 1,
                                            'created_by' => Auth::id()
                                        ]);
                                    }   
                                }
                            }
                        }

                    }while($diff > 0);

                    $loop = true;
                    break;
                }else{
                    $loop = false;
                }
            }
        } while ($loop);



        //GET CLFG MAX PRODUCTION
        $cl_capacity = DB::table('production_capacities')
        ->where('base_model', 'CLFG')
        ->where('remark', 'capacity product')
        ->first();

        $avg_cl_capacity = ceil($total_request /count($dates));

        $loop = true;

        do{
            //GET SCHDULE CLFG PER HARI
            $step_two_cl = DB::table('production_schedules_two_steps')
            ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
            ->where(db::raw('date_format(production_schedules_two_steps.due_date, "%Y-%m")') ,'2021-01')
            ->where('materials.hpl', 'CLFG')
            ->select(
                'production_schedules_two_steps.due_date',
                db::raw('SUM(production_schedules_two_steps.quantity) AS quantity')
            )
            ->groupBy('production_schedules_two_steps.due_date')
            ->get();

            for ($i=0; $i < count($step_two_cl); $i++) {
                if($step_two_cl[$i]->quantity > $avg_cl_capacity){

                    //SCHEDULE HARI H
                    $temp = DB::table('production_schedules_two_steps')
                    ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
                    ->where('production_schedules_two_steps.due_date', $step_two_cl[$i]->due_date)
                    ->where('materials.base_model', '<>', 'CL SILVER')
                    ->select(
                        'production_schedules_two_steps.due_date',
                        'production_schedules_two_steps.material_number',
                        'materials.base_model',
                        'production_schedules_two_steps.quantity'
                    )
                    ->orderBy('production_schedules_two_steps.quantity', 'DESC')
                    ->get();

                    $index_date;
                    for ($j=0; $j < count($dates); $j++) { 
                        if($dates[$j]->week_date == $step_two_cl[$i]->due_date){
                            if($j == count($dates)-1){
                                $index_date = $j;
                            }else{
                                $index_date = $j+1;
                            }
                        }
                    }

                    $next_date = $dates[$index_date]->week_date;
                    $diff = $step_two_cl[$i]->quantity - $avg_cl_capacity;

                    do{
                        for ($x=0; $x < count($temp); $x++) {
                            if($diff > 0){
                                $lot = DB::table('production_capacities')
                                ->where('base_model', $temp[$x]->material_number)
                                ->where('remark', 'lot')
                                ->first();

                                if($lot){
                                    // $exsiting = DB::table('production_schedules_two_steps')
                                    // ->where('material_number', $temp[$x]->material_number)
                                    // ->where('due_date', $step_two_cl[$i]->due_date)
                                    // ->first();

                                    // $update_exsiting = DB::table('production_schedules_two_steps')
                                    // ->where('id', $exsiting->id)
                                    // ->update([
                                    //     'quantity' => $exsiting->quantity - $lot->quantity
                                    // ]);

                                    // $diff -= $lot->quantity;

                                    // $next = DB::table('production_schedules_two_steps')
                                    // ->where('material_number', $temp[$x]->material_number)
                                    // ->where('due_date', $next_date)
                                    // ->first();

                                    // if($next){
                                    //     $update_next = DB::table('production_schedules_two_steps')
                                    //     ->where('id', $next->id)
                                    //     ->update([
                                    //         'quantity' => $next->quantity + $lot->quantity
                                    //     ]);
                                    // }else{
                                    //     $insert = DB::table('production_schedules_two_steps')->insert([
                                    //         'due_date' => $next_date,
                                    //         'material_number' => $temp[$x]->material_number,
                                    //         'quantity' => $lot->quantity,
                                    //         'created_by' => Auth::id()
                                    //     ]);
                                    // }                                

                                }else{
                                    $exsiting = DB::table('production_schedules_two_steps')
                                    ->where('material_number', $temp[$x]->material_number)
                                    ->where('due_date', $step_two_cl[$i]->due_date)
                                    ->first();

                                    $update_exsiting = DB::table('production_schedules_two_steps')
                                    ->where('id', $exsiting->id)
                                    ->update([
                                        'quantity' => $exsiting->quantity - 1
                                    ]);

                                    $diff--;

                                    $next = DB::table('production_schedules_two_steps')
                                    ->where('material_number', $temp[$x]->material_number)
                                    ->where('due_date', $next_date)
                                    ->first();

                                    if($next){
                                        $update_next = DB::table('production_schedules_two_steps')
                                        ->where('id', $next->id)
                                        ->update([
                                            'quantity' => $next->quantity + 1
                                        ]);
                                    }else{
                                        $insert = DB::table('production_schedules_two_steps')->insert([
                                            'due_date' => $next_date,
                                            'material_number' => $temp[$x]->material_number,
                                            'quantity' => 1,
                                            'created_by' => Auth::id()
                                        ]);
                                    }   
                                }
                            }
                        }

                    }while($diff > 0);

                    $loop = true;
                    break;
                }else{
                    $loop = false;
                }
            }
        } while ($loop);

    }

    public function fetchViewScheduleStepOne(){

        // $this->generateScheduleStepOne();
        // $this->generateScheduleStepTwo();

        $get_dates = WeeklyCalendar::where('week_date', 'like', '%2021-01%')
        ->get();

        $sum_forecasts = ProductionForecast::leftJoin('materials', 'materials.material_number', '=', 'production_forecasts.material_number')
        ->where(db::raw('date_format(production_forecasts.forecast_month, "%Y-%m")') ,'2021-01')
        ->where('production_forecasts.quantity', '>', 0)
        ->select(db::raw('date_format(production_forecasts.forecast_month, "%Y-%m") AS month'), 'materials.hpl', db::raw('SUM(production_forecasts.quantity) AS quantity'))
        ->groupBy('month', 'materials.hpl')
        ->get();

        $materials = Material::where('category', 'FG')
        ->orderBy('material_description', 'ASC')
        ->get();

        $step_one = DB::table('production_schedules_one_steps')
        ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
        ->where('materials.hpl', 'CLFG')
        ->get();

        $step_two = DB::table('production_schedules_two_steps')
        ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
        ->where('materials.hpl', 'CLFG')
        ->get();

        $cl_capacities = DB::table('production_capacities')
        ->where('base_model', 'like', '%CL%')
        ->where('remark', 'capacity product')
        ->get();

        $response = array(
            'status' => true,
            'get_dates' => $get_dates,
            'sum_forecasts' => $sum_forecasts,
            'materials' => $materials,
            'step_one' => $step_one,
            'step_two' => $step_two,
            'cl_capacities' => $cl_capacities
        );
        return Response::json($response);
    }


    public function index()
    {
        $materials = Material::where('category', '=', 'FG')->get();

        $locations = Material::where('category', '=', 'FG')
        ->whereNotNull('hpl')
        ->select('hpl', 'category')
        ->distinct()
        ->orderBy('category', 'asc')
        ->orderBy('issue_storage_location', 'asc')
        ->orderBy('hpl', 'asc')
        ->get();

        return view('production_schedules.index', array(
          'locations' => $locations,
          'materials' => $materials
      ))->with('page', 'Production Schedule');
        //
    }

    public function indexKD()
    {

        $materials = Material::where('category', '=', 'KD')->get();

        $locations = Material::where('category', '=', 'KD')
        ->whereNotNull('hpl')
        ->select('hpl', 'category')
        ->distinct()
        ->orderBy('category', 'asc')
        ->orderBy('issue_storage_location', 'asc')
        ->get();

        return view('production_schedules.index_kd', array(
            'locations' => $locations,
            'materials' => $materials
        ))->with('page', 'Production Schedule KD');
        
    }

    public function fetchSchedule(Request $request)
    {
        $due_date = date('Y-m-d', strtotime("first day of -2 month"));

        $production_schedules = ProductionSchedule::leftJoin("materials","materials.material_number","=","production_schedules.material_number")
        ->leftJoin("origin_groups","origin_groups.origin_group_code","=","materials.origin_group_code")
        ->select('production_schedules.id','production_schedules.material_number','production_schedules.due_date','production_schedules.quantity','materials.material_description','origin_groups.origin_group_name', 'materials.hpl')
        ->whereRaw('due_date >= "'.$due_date.'"')
        ->orderByRaw('due_date DESC', 'production_schedules.material_number ASC')
        ->where('materials.category', '=', 'FG')
        ->get();

        return DataTables::of($production_schedules)
        ->addColumn('action', function($production_schedules){
          return '
          <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$production_schedules->id.')">View</button>
          <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$production_schedules->id.')">Edit</button>
          <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$production_schedules->id.',\''.$production_schedules->material_number.'\',\''.$production_schedules->due_date.'\')">Delete</button>';
      })

        ->rawColumns(['action' => 'action'])
        ->make(true);
    }

    public function fetchScheduleKD(Request $request)
    {
        $due_date = date('Y-m-d', strtotime("first day of -1 month"));

        $production_schedules = ProductionSchedule::leftJoin("materials","materials.material_number","=","production_schedules.material_number")
        ->leftJoin("origin_groups","origin_groups.origin_group_code","=","materials.origin_group_code")
        ->select('production_schedules.id','production_schedules.material_number','production_schedules.due_date','production_schedules.quantity', 'production_schedules.actual_quantity', 'materials.material_description','origin_groups.origin_group_name', 'materials.hpl')
        ->whereRaw('due_date >= "'.$due_date.'"')
        ->orderByRaw('due_date DESC', 'production_schedules.material_number ASC')
        ->where('materials.category', '=', 'KD')
        ->get();

        return DataTables::of($production_schedules)
        ->addColumn('action', function($production_schedules){
          return '
          <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$production_schedules->id.')">View</button>
          <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$production_schedules->id.')">Edit</button>
          <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$production_schedules->id.',\''.$production_schedules->material_number.'\',\''.$production_schedules->due_date.'\')">Delete</button>';
      })

        ->rawColumns(['action' => 'action'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	$materials = Material::orderBy('material_number', 'ASC')->get();
    	return view('production_schedules.create', array(
    		'materials' => $materials
    	))->with('page', 'Production Schedule');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$due_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('due_date'))));
    	try
    	{
    		$id = Auth::id();
    		$production_schedule = new ProductionSchedule([
    			'material_number' => $request->get('material_number'),
    			'due_date' => $due_date,
    			'quantity' => $request->get('quantity'),
    			'created_by' => $id
    		]);

    		$production_schedule->save();    

    		$response = array(
    			'status' => true,
    			'production_schedule' => $production_schedule,
    		);
    		return Response::json($response);
    	}
    	catch (QueryException $e){
    		$error_code = $e->errorInfo[1];
    		if($error_code == 1062){
    			return redirect('/index/production_schedule')->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
    		}
    		else{
    			return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
    		}
    	}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
    	$query = "select production_schedule.material_number, production_schedule.due_date, production_schedule.quantity, users.`name`, material_description, origin_group_name, production_schedule.created_at, production_schedule.updated_at from
    	(select material_number, due_date, quantity, created_by, created_at, updated_at from production_schedules where id = "
    	.$request->get('id').") as production_schedule
    	left join materials on materials.material_number = production_schedule.material_number
    	left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
    	left join users on production_schedule.created_by = users.id";

    	$production_schedule = DB::select($query);

    	$response = array(
    		'status' => true,
    		'datas' => $production_schedule
    	);
    	return Response::json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchEdit(Request $request)
    {
      // $materials = Material::orderBy('material_number', 'ASC')->get();
    	$production_schedule = ProductionSchedule::find($request->get("id"));

    	$response = array(
    		'status' => true,
    		'datas' => $production_schedule
    	);
    	return Response::json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editKD(Request $request)
    {
        $due_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('due_date'))));

        try{
            $production_schedule = ProductionSchedule::find($request->get('id'));

            if($prouction_schedule->quantity >= $request->get('actual_quantity')){
                $production_schedule->quantity = $request->get('quantity');
                $production_schedule->save();

                $response = array(
                    'status' => true,
                    'datas' => $production_schedule
                );
                return Response::json($response);
            }
            else{
             $response = array(
                'status' => false,
                'datas' => $production_schedule
            );
             return Response::json($response);
         }
     }
     catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
            return redirect('/index/production_schedule')->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
        }
        else{
            return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
        }
    }
}

public function edit(Request $request)
{
 $due_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('due_date'))));

 try{
  $production_schedule = ProductionSchedule::find($request->get('id'));
  $production_schedule->quantity = $request->get('quantity');
  $production_schedule->save();

  $response = array(
   'status' => true,
   'datas' => $production_schedule
);
  return Response::json($response);
}
catch (QueryException $e){
  $error_code = $e->errorInfo[1];
  if($error_code == 1062){
   return redirect('/index/production_schedule')->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
}
else{
   return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
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
    	$production_schedule = ProductionSchedule::find($request->get("id"));
    	$production_schedule->forceDelete();

    	$response = array(
    		'status' => true
    	);
    	return Response::json($response);
    }

    public function deleteKD(Request $request)
    {
        $production_schedule = ProductionSchedule::find($request->get("id"));

        if($production_schedule->quantity == 0){
            $production_schedule->forceDelete();
        }
        else{
           $response = array(
            'status' => false
        );
           return Response::json($response);   
       }

       $response = array('status' => true);
       return Response::json($response);
   }

   public function destroy(Request $request){
       $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
       $date_to = date('Y-m-d', strtotime($request->get('dateto')));

       $materials = Material::select('material_number');

       foreach($request->get('location') as $location){
        $locations = explode(",", $location);

        $category = $locations[0];
        $hpl = $locations[1];

        $materials = Material::where('hpl', '=', $hpl)
        ->where('category', $category)
        ->select('material_number')
        ->get();

        try{
            $production_schedules = ProductionSchedule::where('due_date', '>=', $date_from)
            ->where('due_date', '<=', $date_to)
            ->whereIn('material_number', $materials)
            ->forceDelete();                
        }
        catch (\Exception $e) {
            return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
        }
    }

    return redirect('/index/production_schedule')
    ->with('status', 'Production schedules has been deleted.')
    ->with('page', 'Production Schedule');
}

    /**
     * Import resource from Text File.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
    	try{
    		if($request->hasFile('production_schedule')){
                // ProductionSchedule::truncate();

    			$id = Auth::id();

    			$file = $request->file('production_schedule');
    			$data = file_get_contents($file);

    			$rows = explode("\r\n", $data);
    			foreach ($rows as $row)
    			{
    				if (strlen($row) > 0) {
    					$row = explode("\t", $row);
    					$production_schedule = new ProductionSchedule([
    						'material_number' => $row[0],
    						'due_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[1]))),
    						'quantity' => $row[2],
    						'created_by' => $id,
    					]);

    					$production_schedule->save();
    				}
    			}
    			return redirect('/index/production_schedule')->with('status', 'New production schedule has been imported.')->with('page', 'Production Schedule');
    		}
    		else
    		{
    			return redirect('/index/production_schedule')->with('error', 'Please select a file.')->with('page', 'Production Schedule');
    		}
    	}

    	catch (QueryException $e){
    		$error_code = $e->errorInfo[1];
    		if($error_code == 1062){
    			return back()->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
    		}
    		else{
    			return back()->with('error', $e->getMessage())->with('page', 'Production Schedule');
    		}

    	}
            //
    }

    public function importKd(Request $request)
    {

        try{
            if($request->hasFile('production_schedule')){
                // ProductionSchedule::truncate();

                $id = Auth::id();

                $file = $request->file('production_schedule');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);
                foreach ($rows as $row)
                {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);
                        $production_schedule =  db::table('production_schedules_one_steps')
                        ->insert([
                            'material_number' => $row[0],
                            'due_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[1]))),
                            'quantity' => $row[2],
                            'created_by' => $id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);


                    }
                }
                return redirect('/index/production_schedule_kd')->with('status', 'New production schedule has been imported.')->with('page', 'Production Schedule');
            }
            else
            {
                return redirect('/index/production_schedule_kd')->with('error', 'Please select a file.')->with('page', 'Production Schedule');
            }
        }

        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Production Schedule');
            }

        }
    }

    public function indexProductionData()
    {
    	$origin_groups = DB::table('origin_groups')->get();
    	$materials = Material::where('category','=','FG')->orderBy('material_number', 'ASC')->get();
    	$models = Material::where('category','=','FG')->orderBy('model', 'ASC')->distinct()->select('model')->get();

    	return view('production_schedules.data', array(
    		'origin_groups' => $origin_groups,
    		'materials' => $materials,
    		'models' => $models,
    		'title' => 'Production Schedule Data',
    		'title_jp' => '生産スケジュールデータ'
    	))->with('page', 'Production Schedule');
    }


    public function fetchProductionData(Request $request)
    {

    	$first = date("Y-m-01", strtotime($request->get("dateTo")));
      // $request->get("material_number");
      // $request->get("model");

      // PRODUCTION SCHEDULE

    	$production_sch = ProductionSchedule::leftJoin("materials", "materials.material_number" ,"=" ,"production_schedules.material_number")
    	->where("due_date", ">=", $first)
    	->where("category", "=", "FG");

    	if ($request->get("dateTo")) {
    		$production_sch = $production_sch->where("due_date", "<=", $request->get("dateTo"));
    	}

    	if ($request->get("product_code") != "") {
    		$production_sch = $production_sch->where("origin_group_code", "=", $request->get("product_code"));
    	}

    	$production_sch = $production_sch->select("due_date", "production_schedules.material_number", "material_description", "quantity","origin_group_code","model")
    	->get();

      // ACT PACKING

    	$flo = FloDetail::leftJoin("materials", "materials.material_number" ,"=" ,"flo_details.material_number")
    	->where(db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'), ">=", $first)
    	->where("category", "=", "FG");

    	if ($request->get("dateTo")) {
    		$flo = $flo->where(db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'), "<=", $request->get("dateTo"));
    	}

    	$flo = $flo->select("flo_details.material_number", db::raw('sum(flo_details.quantity) as packing'), db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d") as date'))
    	->groupBy("flo_details.material_number", db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'))
    	->get();


      // DELIVERY

    	if ($request->get("dateTo")) {
    		$where = ' AND DATE_FORMAT(deliv.created_at, "%Y-%m-%d") <= "'.$request->get("dateTo").'"';
    	} else {
    		$where = '';
    	}

    	if ($request->get("product_code") != "") {
    		$product_codes = $request->get('product_code');
    		$codelength = count($product_codes);
    		$code = "";

    		for($x = 0; $x < $codelength; $x++) {
    			$code = $code."'".$product_codes[$x]."'";
    			if($x != $codelength-1){
    				$code = $code.",";
    			}
    		}
    		$where2 = " and origin_group_code in (".$code.") ";
    	} else {
    		$where2 = '';
    	}

    	$q_deliv = 'select * from (select flomaster.flo_number, flomaster.material_number, sum(flomaster.actual) deliv, flomaster.`status`, DATE_FORMAT(deliv.created_at, "%Y-%m-%d") date from
    	(select flos.flo_number, flos.material_number, actual, `status` from flos where `status` NOT IN (0,1,"m")) as flomaster left join 
    	(select flo_number, created_at from flo_logs where status_code = 2) as deliv on flomaster.flo_number = deliv.flo_number
    	where DATE_FORMAT(deliv.created_at, "%Y-%m-%d") >= "'.$first.'" '. $where .'
    	group by flomaster.material_number, DATE_FORMAT(deliv.created_at, "%Y-%m-%d")) alls
    	left join materials on materials.material_number = alls.material_number
    	where category = "FG" '.$where2.'
    	order by alls.material_number asc, alls.date asc';

    	$deliv = db::select($q_deliv);

    	$response = array(
    		'status' => true,
    		'production_sch' => $production_sch,
    		'packing' => $flo,
    		'deliv' => $deliv
    	);
    	return Response::json($response);
    }

    public function indexProductionMonitoring()
    {
    	$origin_groups = DB::table('origin_groups')->get();

    	return view('production_schedules.monitoring', array(
    		'origin_groups' => $origin_groups,
    		'title' => 'Production Schedule Monitoring',
    		'title_jp' => ''
    	))->with('page', 'Production Schedule');
    }
}
