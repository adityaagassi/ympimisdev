<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\TransactionPartInjection;
use App\StockPartInjection;
use App\PlanMesinInjection;
use App\LogShootMesinInjection;
use App\DetailPartInjection;
use App\CycleTimeMesinInjection;
use App\CapacityPartInjection;
use App\CapacityMesinInjection;
use App\MesinLogInjection;

use App\PlanMesinInjectionTmp;

use App\StatusMesinInjection;
use App\WorkingMesinInjection;
use Response;
use DataTables;
use Carbon\Carbon;


class InjectionsController extends Controller
{
    // ----------------- in
    public function index(){

        return view('injection.index')->with('page', 'Injection')->with('jpn', '???');

    }

    public function in(){

        return view('injection.in')->with('page', 'Injection Stock In')->with('jpn', '???');

    }

    public function indexMachineSchedule()
    {
        return view('injection.schedule_view')->with('page', 'Injection Schedule View')->with('jpn', '???');
    }

    public function scanPartInjeksi(Request $request){
        $part = CapacityPartInjection::where('capacity_part_injections.rfid', '=', $request->get('serialNumber'))->get();

        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Scan Part Success',
        );
        return Response::json($response);
    }

    public function getDataIn(Request $request){
        $date = date('Y-m-d');
        $query = "SELECT gmc,part,total, created_at as tgl_in from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and `status` ='".$request->get('proces')."' order by created_at desc";

        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function sendPart(Request $request)
    {
        $gmc1 = $request->get('gmc');
        $id = Auth::id();

        for ($i=0; $i < sizeof($gmc1); $i++) {
            $part = db::table('capacity_part_injections')
            ->where('gmc','=', $gmc1[$i]) 
            ->get();

            $part2 = new TransactionPartInjection([
                'gmc' => $part[0]->gmc,
                'part' => $part[0]->part_name,
                'total' => $part[0]->capacity,
                'status' => $request->get('process'),
                'created_by' => $id
            ]);
            $part2->save();
        }       


        $response = array(
            'status' => true,

        );
        return Response::json($response);
    }

    public function getDataInOut(){

        $moth = date('Y-m');
        $day = date('Y-m-d');
        $first = date('Y-m-01');
        $yesterday = date('Y-m-d',strtotime(Carbon::yesterday()));

        $query = "
        SELECT stock_all.*,in_out_part_all.stock_in, in_out_part_all.stock_out, ((stock_all.stock +in_out_part_all.stock_in)-in_out_part_all.stock_out) as total  from (
        SELECT in_out2.gmc, in_out2.part_name, ((stock.stock_akhir + in_out2.stock_in)-in_out2.stock_out ) as stock from (
        SELECT * from (
        SELECT * from (
        SELECT inPart.gmc, inPart.part_name, COALESCE(inPart.stock_in,0) as  stock_in from(
        SELECT * from capacity_part_injections 
        LEFT JOIN(
        SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in from transaction_part_injections WHERE `status` ='IN' and DATE_FORMAT(created_at,'%Y-%m-%d') <='".$first."' and DATE_FORMAT(created_at,'%Y-%m-%d') >='".$yesterday."' GROUP BY part,gmc
        ) stock_in on capacity_part_injections.part_name = stock_in.part_in
        ) inPart
        ) inpart

        LEFT JOIN (

        SELECT outPart.gmc as gmc_out, outPart.part_name as part_name_out, COALESCE(outPart.stock_in,0) as  stock_out from(
        SELECT * from capacity_part_injections 
        LEFT JOIN(
        SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in from transaction_part_injections WHERE `status` ='OUT' and DATE_FORMAT(created_at,'%Y-%m-%d') <='".$first."' and DATE_FORMAT(created_at,'%Y-%m-%d') >='".$yesterday."' GROUP BY part,gmc
        ) stock_in on capacity_part_injections.part_name = stock_in.part_in
        ) outPart

        ) as outPart on inpart.part_name = outPart.part_name_out

        ) as in_out

        ) as in_out2

        LEFT JOIN (
        SELECT part, stock_akhir from stock_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m')='".$moth."'
        ) as stock on in_out2.part_name = stock.part
        ) as stock_all

        LEFT JOIN (

        SELECT * from (
        SELECT inpart.gmc, inpart.part_name,inpart.stock_in,outpart.stock_out from (
        SELECT inPart.gmc, inPart.part_name, COALESCE(inPart.stock_in,0) as  stock_in from(
        SELECT * from capacity_part_injections 
        LEFT JOIN(
        SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in from transaction_part_injections WHERE `status` ='IN'  and DATE_FORMAT(created_at,'%Y-%m-%d') >='".$day."' GROUP BY part,gmc
        ) stock_in on capacity_part_injections.part_name = stock_in.part_in
        ) inPart
        ) inpart

        LEFT JOIN (

        SELECT outPart.gmc as gmc_out, outPart.part_name as part_name_out, COALESCE(outPart.stock_in,0) as  stock_out from(
        SELECT * from capacity_part_injections 
        LEFT JOIN(
        SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in from transaction_part_injections WHERE `status` ='OUT'  and DATE_FORMAT(created_at,'%Y-%m-%d') >='".$day."' GROUP BY part,gmc
        ) stock_in on capacity_part_injections.part_name = stock_in.part_in
        ) outPart

        ) as outPart on inpart.part_name = outPart.part_name_out
        ) as in_out_part

        ) as in_out_part_all on stock_all.part_name = in_out_part_all.part_name order by part_name 
        ";
        $part = DB::select($query);
        return DataTables::of($part)
        ->make(true);
    }

    // -------------- end in


    // -------------- out

    public function out(){

        return view('injection.out')->with('page', 'Injection Stock Out')->with('jpn', '???');

    }

    // -------------- end out

    // --------------  dailyStock

    public function dailyStock(){

        return view('injection.dailyStock')->with('page', 'Injection Stock Out')->with('jpn', '???');

    }

    public function getDailyStock(Request $request){
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl.'-d';
        $tgl2 = $tgl.'-01';

        if ($tgl !="") {
            $moth = $request->get('tgl');
            $day = date($tgl1, strtotime(carbon::now()->endOfMonth()));
            $first = date($tgl2);
        }else{
            $moth = date('Y-m');
            $day = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
            $first = date('Y-m-01');
        }

        if ($location =="Blue") {
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }
        elseif ($location =="Green") {
            $reg = "YRS20BG|YRS20GG";
            $reg2 = "YRS20BG|YRS20GG|YRS20GGK";
        }
        elseif ($location =="Pink") {
            $reg = "YRS20BP|YRS20GP";
            $reg2 = "YRS20BP|YRS20GP|YRS20GPK";
        }
        elseif ($location =="Red") {
            $reg = "YRS20BR";
            $reg2 = "YRS20BR";
        }
        elseif ($location =="Brown") {
            $reg = "YRS24BUK";
            $reg2 = "YRS24BUK";
        }
        elseif ($location =="Ivory") {
            $reg = "YRS23|YRS24B MIDDLE";
            $reg2 = "YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII";
        }
        elseif ($location =="Yrf") {
            $reg = "YRF21";
            $reg2 = "YRF21|YRF21K";
        }else{
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }
        
        
        $query22 = "select * from (
        SELECT date_all.week_date, date_all.part, COALESCE(stock.stock_in,0) as stock from (
        SELECT * from (
        SELECT DISTINCT part FROM detail_part_injections WHERE  part REGEXP '".$reg."' 
        ) a
        cross JOIN (
        SELECT week_date FROM weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(week_date,'%Y-%m-%d') <='".$day."'
        ) as date 
        ) date_all
        LEFT JOIN (
        SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from transaction_part_injections WHERE  `status` ='IN' and DATE_FORMAT(created_at,'%Y-%m') ='".$moth."' GROUP BY part,gmc,DATE_FORMAT(created_at,'%Y-%m-%d') 
        ) as stock on date_all.week_date = stock.tgl and date_all.part = stock.part_in  ORDER BY part, week_date
        ) as aa GROUP BY week_date, part
        ";

        $query = "select * from (
        SELECT date_all.week_date, date_all.part, COALESCE(stock.stock_in,0) as stock from (
        SELECT * from (
        SELECT DISTINCT part FROM detail_part_injections WHERE  part REGEXP '".$reg."' 
        ) a
        cross JOIN (

                SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender)               
                 and DATE_FORMAT(week_date,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(week_date,'%Y-%m-%d') <='".$day."'
        ) as date 
        ) date_all
        LEFT JOIN (
        SELECT gmc as gmc_in,part as part_in,sum(total) as stock_in, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from transaction_part_injections WHERE  `status` ='IN' and DATE_FORMAT(created_at,'%Y-%m') ='".$moth."' GROUP BY part,gmc,DATE_FORMAT(created_at,'%Y-%m-%d') 
        ) as stock on date_all.week_date = stock.tgl and date_all.part = stock.part_in  ORDER BY part, week_date
        ) as aa GROUP BY week_date, part
        ";

        $query2="SELECT DISTINCT part FROM detail_part_injections WHERE  part REGEXP '".$reg."' ";

        $query3="SELECT week_date, COALESCE(target,0) as target from (
                SELECT a.*, SUM(quantity) as target from (
                SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$day."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
                where model REGEXP '".$reg2."' ORDER BY due_date
                ) a GROUP BY due_date
                ) aa
                RIGHT JOIN (
                SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender)               
                 and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-30'
        ) as date on aa.due_date = date.week_date ORDER BY week_date
        ";

        $part = DB::select($query);
        $model = DB::select($query2);
        $assy = DB::select($query3);
        $response = array(
            'status' => true,            
            'part' => $part,
            'model' => $model,
            'assy' => $assy,
            'message' => 'Get Part Success',
            'asas' => $tgl1,
        );
        return Response::json($response);
    }

    // -------------- end dailyStock

    // ------------------------- shchedule

    public function schedule(){

        return view('injection.schedule')->with('page', 'Injection Stock Out')->with('jpn', '???');

    }

    public function getSchedule(Request $request){

        $from = $request->get('from');
        $to = $request->get('toa');

        $day = date('Y-m-d');

        $first = date('Y-m-01');
        $last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
        $month = date('Y-m');
        $years = date('Y');

        $query2 = "SELECT target_all.material_number,  target_all.model, target_all.part, target_all.part_code, target_all.color,target_all.target,target_all.stock,target_all.max_day,target_all.qty_hako, target_all.cycle, target_all.shoot, CEILING(ROUND((target - stock) / qty_hako,2)) as target_hako, CEILING((CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako)/ mesin) as target_hako_qty ,COALESCE(mesin.mesin,0) mesin, COALESCE(working,'-') working, target_all.due_date  FROM (
        SELECT total_all.material_number, total_all.due_date, total_all.model, total_all.part, total_all.part_code, total_all.color, 
        (total_all.total+(total_all.total / 10))  as target, stock.stock as stock, total_all.max_day,total_all.qty_hako, total_all.cycle, total_all.shoot from (


        SELECT target.*,cycle_time_mesin_injections.cycle, cycle_time_mesin_injections.shoot, cycle_time_mesin_injections.qty, 
        ROUND((82800  / cycle_time_mesin_injections.cycle  )*cycle_time_mesin_injections.shoot,0) as max_day,cycle_time_mesin_injections.qty_hako  from (
        SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, SUM(quantity) as total from (
        SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$last."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        ) as target_model
        CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
        WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d')<='".$to."' and DATE_FORMAT(week_date,'%Y')='".$years."')
        GROUP BY part,color,part_code ORDER BY due_date
        ) target

        LEFT JOIN cycle_time_mesin_injections 
        on target.part_code = cycle_time_mesin_injections.part 
        and target.color = cycle_time_mesin_injections.color
        ORDER BY part


        ) total_all 

        LEFT JOIN (
        SELECT  part, (( SUM(stock_akhir) + SUM(total_in) )-SUM(total_out)) stock from (
        SELECT  part, stock_akhir, 0 as total_in, 0 as total_out from stock_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m')='".$month."'
        UNION all
        SELECT part,0 as stock_akhir ,total as total_in, 0 as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(created_at,'%Y-%m-%d') <='".$last."' and `status` ='IN'
        UNION all
        SELECT part,0 as stock_akhir ,0 as total_in, total as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(created_at,'%Y-%m-%d') <='".$last."' and `status` ='OUT'
        ) as stock GROUP BY part

        ) as stock on total_all.part = stock.part

        ) as target_all

        LEFT JOIN (
        SELECT part,color, SUM(qty) as mesin, GROUP_CONCAT(working_mesin_injections.mesin) as working from working_mesin_injections
        LEFT JOIN status_mesin_injections on working_mesin_injections.mesin = status_mesin_injections.mesin
        where status_mesin_injections.`status` !='OFF'
        GROUP BY part,color ORDER BY mesin
        ) as mesin on target_all.part_code = mesin.part and target_all.color = mesin.color

        
        WHERE (CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako) > 0       
        ORDER BY due_date
        ";

        $query = "SELECT target_all.material_number,  target_all.model, target_all.part, target_all.part_code, target_all.color,target_all.target,target_all.stock,target_all.max_day,target_all.qty_hako, target_all.cycle, target_all.shoot, CEILING(ROUND((target - stock) / qty_hako,2)) as target_hako, CEILING((CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako)/ mesin) as target_hako_qty ,COALESCE(mesin.mesin,0) mesin, COALESCE(working,'-') working, target_all.due_date  FROM (
        SELECT total_all.material_number, total_all.due_date, total_all.model, total_all.part, total_all.part_code, total_all.color, 
        (total_all.total)  as target, stock.stock as stock, total_all.max_day,total_all.qty_hako, total_all.cycle, total_all.shoot from (


        SELECT target.*,cycle_time_mesin_injections.cycle, cycle_time_mesin_injections.shoot, cycle_time_mesin_injections.qty, 
        ROUND((82800  / cycle_time_mesin_injections.cycle  )*cycle_time_mesin_injections.shoot,0) as max_day,cycle_time_mesin_injections.qty_hako  from (
        SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, SUM(quantity) as total from (
        SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$last."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        ) as target_model
        CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
        WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d')<='".$to."' and DATE_FORMAT(week_date,'%Y')='".$years."')
        GROUP BY part,color,part_code ORDER BY due_date
        ) target

        LEFT JOIN cycle_time_mesin_injections 
        on target.part_code = cycle_time_mesin_injections.part 
        and target.color = cycle_time_mesin_injections.color
        ORDER BY part


        ) total_all 

        LEFT JOIN (
        SELECT  part, (( SUM(stock_akhir) + SUM(total_in) )-SUM(total_out)) stock from (
        SELECT  part, stock_akhir, 0 as total_in, 0 as total_out from stock_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m')='".$month."'
        UNION all
        SELECT part,0 as stock_akhir ,total as total_in, 0 as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(created_at,'%Y-%m-%d') <='".$last."' and `status` ='IN'
        UNION all
        SELECT part,0 as stock_akhir ,0 as total_in, total as total_out from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(created_at,'%Y-%m-%d') <='".$last."' and `status` ='OUT'
        ) as stock GROUP BY part

        ) as stock on total_all.part = stock.part

        ) as target_all

        LEFT JOIN (
        SELECT part,color, SUM(qty) as mesin, GROUP_CONCAT(working_mesin_injections.mesin) as working from working_mesin_injections
        LEFT JOIN status_mesin_injections on working_mesin_injections.mesin = status_mesin_injections.mesin
        where status_mesin_injections.`status` !='OFF'
        GROUP BY part,color ORDER BY mesin
        ) as mesin on target_all.part_code = mesin.part and target_all.color = mesin.color

        
        WHERE (CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako) > 0       
        ORDER BY due_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'a' => $to,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }


    public function getStatusMesin(Request $request){             


        $query = "SELECT mesin,`status` from status_mesin_injections";

        $mesin = DB::select($query);
        $response = array(
            'status' => true,            
            'mesin' => $mesin,
            'message' => 'Get Mesin Success',
            
        );
        return Response::json($response);
    }

    public function getDateWorking(Request $request){ 

        $max = $request->get('max');

        $date = date('Y-m-d') ;           
        
        
        $query = "SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' ORDER BY week_date asc limit ".$max."";

        $mesin = DB::select($query);
        $response = array(
            'status' => true,            
            'tgl' => $mesin,
            'message' => 'Get Date Success',
            
        );
        return Response::json($response);
    }



    public function saveSchedule(Request $request){

        $id = Auth::id();
        try{

        // m1

            $m1s = $request->get('PostMESIN1');

            foreach ($m1s as $m1 ) {
                if (strlen($m1) > 0) {
                 $m1 = explode("#", $m1);

                 $plan = new PlanMesinInjection([
                    'mesin' => 'Mesin 1',
                    'part' => $m1[2], 
                    'qty' =>  $m1[3], 
                    'color' =>  $m1[1],
                    'due_date' =>  $m1[0],    
                    'created_by' => $id,
                ]);

                 $plan->save();
             }
         }

        // end  m1

        // m2

         $m2s = $request->get('PostMESIN2');

         foreach ($m2s as $m2 ) {
            if (strlen($m2) > 0) {
             $m2 = explode("#", $m2);

             $plan = new PlanMesinInjection([
                'mesin' => 'Mesin 2',
                'part' => $m2[2], 
                'qty' =>  $m2[3], 
                'color' =>  $m2[1],
                'due_date' =>  $m2[0],    
                'created_by' => $id,
            ]);

             $plan->save();
         }
     }

        // end  m2

        // m3

     $m3s = $request->get('PostMESIN3');

     foreach ($m3s as $m3 ) {
        if (strlen($m3) > 0) {
         $m3 = explode("#", $m3);

         $plan = new PlanMesinInjection([
            'mesin' => 'Mesin 3',
            'part' => $m3[2], 
            'qty' =>  $m3[3], 
            'color' =>  $m3[1],
            'due_date' =>  $m3[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
     }

            // end  m3

            // m4

     $m4s = $request->get('PostMESIN4');

     foreach ($m4s as $m4 ) {
        if (strlen($m4) > 0) {
         $m4 = explode("#", $m4);

         $plan = new PlanMesinInjection([
            'mesin' => 'Mesin 4',
            'part' => $m4[2], 
            'qty' =>  $m4[3], 
            'color' =>  $m4[1],
            'due_date' =>  $m4[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m4

            // m5

    $m5s = $request->get('PostMESIN5');

    foreach ($m5s as $m5 ) {
        if (strlen($m5) > 0) {
         $m5 = explode("#", $m5);

         $plan = new PlanMesinInjection([
            'mesin' => 'Mesin 5',
            'part' => $m5[2], 
            'qty' =>  $m5[3], 
            'color' =>  $m5[1],
            'due_date' =>  $m5[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m5

            // m6

    $m6s = $request->get('PostMESIN6');

    foreach ($m6s as $m6 ) {
        if (strlen($m6) > 0) {
         $m6 = explode("#", $m6);

         $plan = new PlanMesinInjection([
            'mesin' => 'Mesin 6',
            'part' => $m6[2], 
            'qty' =>  $m6[3], 
            'color' =>  $m6[1],
            'due_date' =>  $m6[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m6

            // m7

    $m7s = $request->get('PostMESIN7');

    foreach ($m7s as $m7 ) {
        if (strlen($m7) > 0) {
         $m7 = explode("#", $m7);

         $plan = new PlanMesinInjection([
            'mesin' => 'Mesin 7',
            'part' => $m7[2], 
            'qty' =>  $m7[3], 
            'color' =>  $m7[1],
            'due_date' =>  $m7[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m7

            // m8

    $m8s = $request->get('PostMESIN8');

    foreach ($m8s as $m8 ) {
        if (strlen($m8) > 0) {
         $m8 = explode("#", $m8);

         $plan = new PlanMesinInjection([
            'mesin' => 'Mesin 8',
            'part' => $m8[2], 
            'qty' =>  $m8[3], 
            'color' =>  $m8[1],
            'due_date' =>  $m8[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m8

            // m9

    $m9s = $request->get('PostMESIN9');

    foreach ($m9s as $m9 ) {
        if (strlen($m9) > 0) {
         $m9 = explode("#", $m9);

         $plan = new PlanMesinInjection([
            'mesin' => 'Mesin 9',
            'part' => $m9[2], 
            'qty' =>  $m9[3], 
            'color' =>  $m9[1],
            'due_date' =>  $m9[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m9



            // m11

    $m11s = $request->get('PostMESIN11');

    foreach ($m11s as $m11 ) {
        if (strlen($m11) > 0) {
         $m11 = explode("#", $m11);

         $plan = new PlanMesinInjection([
            'mesin' => 'Mesin 11',
            'part' => $m11[2], 
            'qty' =>  $m11[3], 
            'color' =>  $m11[1],
            'due_date' =>  $m11[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m11



    $response = array(
        'status' => true,   
        'message' => 'Make Schedule Success',

    );
    return Response::json($response);

    }
    catch (QueryException $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
    }
    }


    public function getChartPlan(Request $request){

        $from = $request->get('from');
        $to = $request->get('toa');

        $first = date('Y-m-01');
        $last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));        
        
        
        $query = " select target.*, SUM(quantity) as qty from (
        SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color from (
                SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$last."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
                ) as target_model
                CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
                WHERE due_date >='".$from."' and due_date <='".$to."' 
                ORDER BY due_date asc

                ) as target GROUP BY due_date
        ";

        $query2 = "SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."' ORDER BY week_date asc ";

        $query3 = "SELECT a.*, SUM(qty) as total from (
        SELECT color,part,due_date,qty from plan_mesin_injection_tmps 
        ) a GROUP BY due_date
        ";

        // $query4 ="SELECT mesin,due_date, SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) as color_p,SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 2) as part_p
        // from plan_mesin_injection_tmps  ORDER BY  id 
        // ";

        $query5 ="
        SELECT * from (
        SELECT *, COUNT(mesin) total from (
        SELECT mesin,GROUP_CONCAT(due_date)due_date , SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) as color_p,
        SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 2) as part_p
        from plan_mesin_injection_tmps WHERE SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) !='OFF'  GROUP BY due_date,mesin,color_p,part_p ORDER BY  id 
        ) a GROUP BY mesin 
        ) aa WHERE total > 1
        ";

        $query4 ="SELECT a.due_date, COALESCE(total,1) as total from (
                SELECT * from (
                SELECT c.*, COUNT(c.mesin) total from (
        SELECT b.due_date,b.mesin from (
        SELECT a.*, GROUP_CONCAT(a.due_date) due_date_2 from (
        SELECT mesin,due_date , SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) as color_p,
        SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 2) as part_p
        from plan_mesin_injection_tmps WHERE SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) !='OFF'

        ) a  GROUP BY mesin,color_p,part_p
        ) b 
                ) c GROUP BY mesin
                
                ) d WHERE total > 1
                ) target
                
                RIGHT JOIN (
                SELECT DISTINCT(due_date) from plan_mesin_injection_tmps
                ) a on target.due_date = a.due_date
        ";

        $query4a ="SELECT aa.due_date, SUM(aa.total)total from (
                SELECT a.due_date, COALESCE(total,0) as total from (
                SELECT * from (
                SELECT c.*, COUNT(c.mesin) total from (
        SELECT RIGHT(b.due_date_2,10) as due_date_all,b.mesin from (
        SELECT a.*, GROUP_CONCAT(a.due_date) due_date_2 from (
        SELECT mesin,due_date , SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) as color_p,
        SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 2) as part_p
        from plan_mesin_injection_tmps WHERE SPLIT_STRING(plan_mesin_injection_tmps.color,' - ', 1) !='OFF'

        ) a  GROUP BY mesin,color_p,part_p
        ) b 
                ) c GROUP BY mesin
                
                ) d WHERE total > 1
                ) target
                
                RIGHT JOIN (
                SELECT DISTINCT(due_date) from plan_mesin_injection_tmps
                ) a on target.due_date_all = a.due_date
                ) aa GROUP BY due_date

        ";
        
        $part = DB::select($query);
        $tgl = DB::select($query2);
        $plan = DB::select($query3);
        $molding = DB::select($query4);
        $response = array(
            'status' => true,            
            'part' => $part,
            'tgl' => $tgl,
            'plan' => $plan,
            'molding' => $molding,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

 // ------------------------- end shchedule

    // ------------------------------- operator mesin

public function opmesin()
{
    $title = 'Machine Operationing';
    $title_jp = '???';
    return view('injection.machine', array(
        'title' => $title,
        'title_jp' => $title_jp,
    ))->with('page', 'Machine Operationing');
}

public function getDataMesinShootLog(Request $request){

    $time = date('H:m:s');
    if ($time > '07:10:00') {
        $date = date('Y-m-d');
        $last = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
    }else{
        $date = date('Y-m-d', strtotime(Carbon::yesterday()));
        $last = date('Y-m-d');
    }  

    $query = "SELECT mesin, color, part, SUM(target) as target, SUM(act) as act, (SUM(act) - SUM(target)  ) as minus  from (
    SELECT mesin,color,part,qty as target, 0 as act from plan_mesin_injections 
    WHERE mesin='Mesin 1' and due_date='".$date."'
    UNION all
    SELECT mesin, color, part,0 as target, qty as act from log_shoot_mesin_injections 
    WHERE created_at >= '".$date." 00:00:00' and created_at <= '".$last." 07:10:00'
) a GROUP BY part,mesin,color";

$target = DB::select($query);
$response = array(
    'status' => true,            
    'target' => $target,
    'message' => 'Get Target Success',

);
return Response::json($response);
}

public function getDataMesinStatusLog(Request $request){

    $date = date('Y-m-d');

    $query = "SELECT `status`, reason, DATE_FORMAT(created_at,'%H:%i:%s') as start_time, COALESCE(DATE_FORMAT(deleted_at,'%H:%i:%s'),'-') as end_time  from mesin_log_injections WHERE mesin ='Mesin 1' and DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' ORDER BY created_at desc";

    $log = DB::select($query);
    $response = array(
        'status' => true,            
        'log' => $log,
        'message' => 'Get Log Status Success',

    );
    return Response::json($response);
}

public function inputStatusMesin(Request $request)
{
    $mesin = $request->get('mesin');
    $statusa = $request->get('statusa');
    $Reason = $request->get('Reason');
    $id = Auth::id();

    $master = MesinLogInjection::where('status','!=' ,$statusa)
    ->delete();

    try{
        $plan = new MesinLogInjection([
            'mesin' => $mesin,
            'status' => $statusa, 
            'reason' =>  $Reason,               
            'created_by' => $id,
        ]);

        $plan->save();

        $response = array(
            'status' => true,   
            'message' => 'Change Status Mesin Success',
            
        );
        return Response::json($response);

    }
    catch (QueryException $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
    }

}

public function getStatusMesian(Request $request){

    $time = date('H:m:s');
    if ($time > '07:10:00') {
        $date = date('Y-m-d');
        $last = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
    }else{
        $date = date('Y-m-d', strtotime(Carbon::yesterday()));
        $last = date('Y-m-d');
    }  

    $query = "SELECT mesin, color, part, SUM(target) as target, SUM(act) as act, (SUM(act) - SUM(target)  ) as minus  from (
    SELECT mesin,color,part,qty as target, 0 as act from plan_mesin_injections 
    WHERE mesin='Mesin 1' and due_date='".$date."'
    UNION all
    SELECT mesin, color, part,0 as target, qty as act from log_shoot_mesin_injections 
    WHERE created_at >= '".$date." 00:00:00' and created_at <= '".$last." 07:10:00'
) a GROUP BY part,mesin,color";

$target = DB::select($query);
$response = array(
    'status' => true,            
    'target' => $target,
    'message' => 'Get Target Success',

);
return Response::json($response);
}

    // ------------------------------- end operator mesin



public function fetchSchedule()
{
    $sch = db::select("SELECT mesin, plan_mesin_injections.color, plan_mesin_injections.qty, due_date, shoot, cycle.cycle, plan_mesin_injections.part FROM `plan_mesin_injections` left join cycle_time_mesin_injections as cycle on cycle.part = SPLIT_STRING(plan_mesin_injections.color,' - ', 1) and cycle.color = SPLIT_STRING(plan_mesin_injections.color,' - ', 2)
        order by due_date asc, plan_mesin_injections.id asc");

    $response = array(
        'status' => true,            
        'schedule' => $sch

    );
    return Response::json($response);

}

public function saveScheduleTmp(Request $request){

        $id = Auth::id();
        $date = date('Y-m-d');
        $master = PlanMesinInjectionTmp::whereNull('deleted_at')
        ->forceDelete();
        try{

        // m1

            $m1s = $request->get('PostMESIN1');

            foreach ($m1s as $m1 ) {
                if (strlen($m1) > 0) {
                 $m1 = explode("#", $m1);

                 $plan = new PlanMesinInjectionTmp([
                    'mesin' => 'Mesin 1',
                    'part' => $m1[2], 
                    'qty' =>  $m1[3], 
                    'color' =>  $m1[1],
                    'due_date' =>  $m1[0],    
                    'created_by' => $id,
                ]);

                 $plan->save();
             }
         }

        // end  m1

        // m2

         $m2s = $request->get('PostMESIN2');

         foreach ($m2s as $m2 ) {
            if (strlen($m2) > 0) {
             $m2 = explode("#", $m2);

             $plan = new PlanMesinInjectionTmp([
                'mesin' => 'Mesin 2',
                'part' => $m2[2], 
                'qty' =>  $m2[3], 
                'color' =>  $m2[1],
                'due_date' =>  $m2[0],    
                'created_by' => $id,
            ]);

             $plan->save();
         }
     }

        // end  m2

        // m3

     $m3s = $request->get('PostMESIN3');

     foreach ($m3s as $m3 ) {
        if (strlen($m3) > 0) {
         $m3 = explode("#", $m3);

         $plan = new PlanMesinInjectionTmp([
            'mesin' => 'Mesin 3',
            'part' => $m3[2], 
            'qty' =>  $m3[3], 
            'color' =>  $m3[1],
            'due_date' =>  $m3[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
     }

            // end  m3

            // m4

     $m4s = $request->get('PostMESIN4');

     foreach ($m4s as $m4 ) {
        if (strlen($m4) > 0) {
         $m4 = explode("#", $m4);

         $plan = new PlanMesinInjectionTmp([
            'mesin' => 'Mesin 4',
            'part' => $m4[2], 
            'qty' =>  $m4[3], 
            'color' =>  $m4[1],
            'due_date' =>  $m4[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m4

            // m5

    $m5s = $request->get('PostMESIN5');

    foreach ($m5s as $m5 ) {
        if (strlen($m5) > 0) {
         $m5 = explode("#", $m5);

         $plan = new PlanMesinInjectionTmp([
            'mesin' => 'Mesin 5',
            'part' => $m5[2], 
            'qty' =>  $m5[3], 
            'color' =>  $m5[1],
            'due_date' =>  $m5[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m5

            // m6

    $m6s = $request->get('PostMESIN6');

    foreach ($m6s as $m6 ) {
        if (strlen($m6) > 0) {
         $m6 = explode("#", $m6);

         $plan = new PlanMesinInjectionTmp([
            'mesin' => 'Mesin 6',
            'part' => $m6[2], 
            'qty' =>  $m6[3], 
            'color' =>  $m6[1],
            'due_date' =>  $m6[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m6

            // m7

    $m7s = $request->get('PostMESIN7');

    foreach ($m7s as $m7 ) {
        if (strlen($m7) > 0) {
         $m7 = explode("#", $m7);

         $plan = new PlanMesinInjectionTmp([
            'mesin' => 'Mesin 7',
            'part' => $m7[2], 
            'qty' =>  $m7[3], 
            'color' =>  $m7[1],
            'due_date' =>  $m7[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m7

            // m8

    $m8s = $request->get('PostMESIN8');

    foreach ($m8s as $m8 ) {
        if (strlen($m8) > 0) {
         $m8 = explode("#", $m8);

         $plan = new PlanMesinInjectionTmp([
            'mesin' => 'Mesin 8',
            'part' => $m8[2], 
            'qty' =>  $m8[3], 
            'color' =>  $m8[1],
            'due_date' =>  $m8[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m8

            // m9

    $m9s = $request->get('PostMESIN9');

    foreach ($m9s as $m9 ) {
        if (strlen($m9) > 0) {
         $m9 = explode("#", $m9);

         $plan = new PlanMesinInjectionTmp([
            'mesin' => 'Mesin 9',
            'part' => $m9[2], 
            'qty' =>  $m9[3], 
            'color' =>  $m9[1],
            'due_date' =>  $m9[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m9



            // m11

    $m11s = $request->get('PostMESIN11');

    foreach ($m11s as $m11 ) {
        if (strlen($m11) > 0) {
         $m11 = explode("#", $m11);

         $plan = new PlanMesinInjectionTmp([
            'mesin' => 'Mesin 11',
            'part' => $m11[2], 
            'qty' =>  $m11[3], 
            'color' =>  $m11[1],
            'due_date' =>  $m11[0],    
            'created_by' => $id,
        ]);

         $plan->save();
     }
    }

            // end  m11



    $response = array(
        'status' => true,   
        'message' => 'Make Schedule Success',

    );
    return Response::json($response);

    }
    catch (QueryException $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
    }
    }




}
