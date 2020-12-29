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
use App\NgLogMesinInjection;
use App\NgTempMesinInjection;
use App\MoldingInjectionLog;
use App\MoldingInjectionMaster;
use App\HistoryMoldingInjectionTemp;
use App\HistoryMoldingInjection;
use App\MaintenanceMoldingTemp;
use App\MaintenanceMoldingLog;
use App\PlanMesinInjectionTmp;
use App\StatusMesinInjection;
use App\WorkingMesinInjection;
use App\InjectionTag;
use App\InjectionMoldingMaster;
use App\InjectionProcessTemp;
use App\InjectionProcessLog;
use App\InjectionMoldingLog;
use App\InjectionInventory;
use App\InjectionTransaction;
use App\InjectionHistoryMoldingTemp;
use App\InjectionHistoryMoldingLog;
use App\InjectionMaintenanceMoldingLog;
use App\InjectionMaintenanceMoldingTemp;
use App\InjectionMachineMaster;
use App\InjectionMachineWork;
use App\InjectionMachineWorkingLog;
use App\InjectionMachineLog;
use App\InjectionDryerLog;
use App\InjectionDryer;
use App\InjectionResin;
use App\EmployeeSync;
use Response;
use DataTables;
use Carbon\Carbon;
use App\Inventory;

class InjectionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
        $this->mesin = [
          'Mesin 1',
          'Mesin 2',
          'Mesin 3',
          'Mesin 4',
          'Mesin 5',
          'Mesin 6',
          'Mesin 7',
          'Mesin 8',
          'Mesin 9',
          'Mesin 11',
      ];

      $this->dryer = [
          '1',
          '2 & 6',
          '3',
          '4',
          '5',
          '7',
          '8',
          '9',
      ];

      $this->color = [
          'BEIGE',
          'IVORY',
          'SKELTON',
      ];

      $this->part = [
          'MJB',
          'MJG',
          'BJ',
          'FJ',
          'HJ',
          'A YRF B',
          'A YRF H',
          'A YRF S',
      ];

      $this->model = [
          'YRS',
          'YRF',
      ];

      $this->jam_istirahat_biasa_1 = array(
                    'ist1_start' => '08:00:00',
                    'ist1_end' => '08:40:00', 
                    'ist2_start' => '11:15:00',
                    'ist2_end' => '12:40:00',
                    'ist3_start' => '13:20:00',
                    'ist3_end' => '14:00:00',
                );
      $this->jam_istirahat_jumat_1 = array(
                    'ist1_start' => '08:00:00',
                    'ist1_end' => '08:40:00', 
                    'ist2_start' => '11:15:00',
                    'ist2_end' => '13:10:00',
                    'ist3_start' => '13:50:00',
                    'ist3_end' => '14:30:00',
                );

      $this->jam_istirahat_2 = array(
                    'ist1_start' => '17:35:00',
                    'ist1_end' => '18:10:00', 
                    'ist2_start' => '20:00:00',
                    'ist2_end' => '21:10:00',
                    'ist3_start' => '22:20:00',
                    'ist3_end' => '22:50:00',
                );
  }


  public function index(){

    return view('injection.index')->with('page', 'Injection')->with('jpn', '成形');

    }

    public function in(){

        return view('injection.in')->with('page', 'Injection Stock In')->with('jpn', '???');

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

    public function scanNewTagInjeksi(Request $request){
        $tag = InjectionTag::where('tag', '=', $request->get('tag'))->where('operator_id', '=', null)->first();

        if (count($tag) > 0) {
            $response = array(
                'status' => true,
                'tag' => $tag,
                'message' => 'Scan Product Tag Success',
            );
        }
        else{
            $response = array(
                'status' => false,
                'message' => 'Tag Invalid'
            );
            return Response::json($response);
        }
        return Response::json($response);
    }

    public function scanPartMolding(Request $request){
        $part = InjectionMoldingMaster::where('status_mesin', '=', $request->get('mesin'))->first();

        if (count($part) > 0) {
            $product = DB::SELECT("SELECT id,part_name,CONCAT(SUBSTRING_INDEX(part_name, ' ', 1),'-',UPPER(part_code),'-',UPPER(color),'-',UPPER(gmc)) as product FROM `injection_parts` where remark = 'injection' and color = '".$request->get('color')."' and part_code = '".$part->product."' and deleted_at is null ORDER BY part_name desc");

            $response = array(
                'status' => true,
                'part' => $part,
                'product' => $product,
                'message' => 'Scan Molding Tag Success',
            );
            return Response::json($response);
        }else{
            $response = array(
                'status' => false,
                'message' => 'Molding Invalid'
            );
            return Response::json($response);
        }
    }

    public function scanInjectionOperator(Request $request){

        $nik = $request->get('employee_id');

        if(strlen($nik) > 9){
            $nik = substr($nik,0,9);
        }

        $employee = db::table('employees')->where('tag', 'like', '%'.$nik.'%')->first();

        if(count($employee) > 0 ){
            $response = array(
                'status' => true,
                'message' => 'Logged In',
                'employee' => $employee
            );
            return Response::json($response);
        }
        else{
            $response = array(
                'status' => false,
                'message' => 'Employee ID Invalid'
            );
            return Response::json($response);
        }
    }

    public function getNewProductCavity(Request $request)
    {
        try {
            $product = DB::SELECT("SELECT id,part_name,CONCAT(SUBSTRING_INDEX(part_name, ' ', 1),'-',UPPER(part_code),'-',UPPER(color),'-',UPPER(gmc)) as product FROM `injection_parts` where remark = 'injection' and color = '".$request->get('color')."' and part_code = '".$request->get('part')."' and deleted_at is null ORDER BY part_name desc");

            $response = array(
                'status' => true,
                'product' => $product,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => 'Data Tidak Tersedia'
            );
            return Response::json($response);
        }
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

        $query2week = "SELECT target_all.material_number,  target_all.model, target_all.part, target_all.part_code, target_all.color,target_all.target,target_all.stock,target_all.max_day,target_all.qty_hako, target_all.cycle, target_all.shoot, CEILING(ROUND((target - stock) / qty_hako,2)) as target_hako, CEILING((CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako)/ mesin) as target_hako_qty ,COALESCE(mesin.mesin,0) mesin, COALESCE(working,'-') working, target_all.due_date  FROM (
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

        $querymonth = "SELECT target_all.material_number,  target_all.model, target_all.part, target_all.part_code, target_all.color,target_all.target,target_all.stock,target_all.max_day,target_all.qty_hako, target_all.cycle, target_all.shoot, CEILING(ROUND((target - stock) / qty_hako,2)) as target_hako, CEILING((CEILING(ROUND((target - stock) / qty_hako,2))* qty_hako)/ mesin) as target_hako_qty ,COALESCE(mesin.mesin,0) mesin, COALESCE(working,'-') working, target_all.due_date  FROM (
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
        GROUP BY part,color,part_code,DAYOFWEEK(due_date) ORDER BY due_date
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
        SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, quantity as total from (
        SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$first."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$last."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        ) as target_model
        CROSS join  detail_part_injections on target_model.model = detail_part_injections.model
        WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d')<='".$to."' and DATE_FORMAT(week_date,'%Y')='".$years."')
        ORDER BY due_date
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


        $querytarget = " select target.*, SUM(quantity) as qty from (
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

        $query ="SELECT week_date, SUM(blue) blue, SUM(green) green, SUM(pink) pink, SUM(red) red, SUM(brown) brown, SUM(ivory) ivory, SUM(yrf) yrf from (
        SELECT date.week_date, COALESCE(quantity,0) as blue, 0 as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        UNION ALL

        SELECT date.week_date, 0 as blue, COALESCE(quantity,0) as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BG|YRS20GG|YRS20GGK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        UNION ALL

        SELECT date.week_date, 0 as blue, 0 as green, COALESCE(quantity,0) as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BP|YRS20GP|YRS20GPK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        UNION ALL

        SELECT date.week_date, 0 as blue, 0 as green, 0 as pink, COALESCE(quantity,0) as red, 0 as brown, 0 as ivory, 0 as yrf     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BR'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        UNION ALL

        SELECT date.week_date, 0 as blue, 0 as green, 0 as pink, 0 as red, COALESCE(quantity,0) as brown, 0 as ivory, 0 as yrf     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS24BUK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        UNION ALL

        SELECT date.week_date, 0 as blue, 0 as green, 0 as pink, 0 as red, 0 as brown, COALESCE(quantity,0) as ivory, 0 as yrf     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        UNION ALL

        SELECT date.week_date, 0 as blue, 0 as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, COALESCE(quantity,0) as yrf     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRF21|YRF21K'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        ) total GROUP BY week_date

        ";

        $query2 = "SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."' ORDER BY week_date asc ";

        $query3mesin = "SELECT a.*, SUM(qty) as total from (
        SELECT color,part,due_date,qty from plan_mesin_injection_tmps 
        ) a GROUP BY due_date
        ";

        $query3 ="SELECT week_date, SUM(blue) blue, SUM(green) green, SUM(pink) pink, SUM(red) red, SUM(brown) brown, SUM(ivory) ivory, SUM(yrf) yrf from (                                                                SELECT week_date, COALESCE(target,0) as blue,  0 as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf FROM (                                                        
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                                                
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        UNION all

        SELECT week_date, 0 as blue,  COALESCE(target,0) as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf                                                                FROM (                                                          
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                                                
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        UNION all

        SELECT week_date, 0 as blue,  0 as green, COALESCE(target,0) as pink, 0 as red, 0 as brown, 0 as ivory, 0 as yrf                                                                FROM (                                                          
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                                                
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        UNION all

        SELECT week_date, 0 as blue,  0 as green, 0 as pink, COALESCE(target,0) as red, 0 as brown, 0 as ivory, 0 as yrf                                                                FROM (                                                          
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                                                
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        UNION all

        SELECT week_date, 0 as blue,  0 as green, 0 as pink, 0 as red, COALESCE(target,0) as brown, 0 as ivory, 0 as yrf                                                                FROM (                                                          
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                                                
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        UNION all

        SELECT week_date, 0 as blue,  0 as green, 0 as pink, 0 as red, 0 as brown, COALESCE(target,0) as ivory, 0 as yrf                                                                FROM (                                                          
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                                                
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        UNION all

        SELECT week_date, 0 as blue,  0 as green, 0 as pink, 0 as red, 0 as brown, 0 as ivory, COALESCE(target,0) as yrf                                                                FROM (                                                          
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRF21' AND
        color like 'A YRF S%' 
        ) a GROUP BY due_date   
        ) target                                                                                                                                
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date
        ) total GROUP BY week_date

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

    public function injection_machine()
    {
        $title = 'Injection Machine';
        $title_jp = '成形機';
        $ng_lists = DB::table('ng_lists')->where('location', '=', 'Recorder')->get();

        return view('injection.machine_injection', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'ng_lists' => $ng_lists,
            'mesin' => $this->mesin,
            'dryer' => $this->dryer,
            'name' => Auth::user()->name
        ))->with('page', 'Injection Machine');
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

        $mesin = $request->get('mesin');
        $date = date('Y-m-d');

        $query = "SELECT `status`, reason, DATE_FORMAT(created_at,'%H:%i:%s') as start_time, COALESCE(DATE_FORMAT(deleted_at,'%H:%i:%s'),'-') as end_time  from mesin_log_injections WHERE mesin ='".$mesin."' and DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' ORDER BY created_at desc";

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

        $master = MesinLogInjection::where('mesin','=' ,$mesin)
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

    public function create_temp(Request $request)
    {
        try {
            $id_user = Auth::id();
            $injection = InjectionProcessTemp::create([
                // 'tag_product' => $request->get('tag_product'),
                'tag_molding' => $request->get('tag_molding'),
                'operator_id' => $request->get('operator_id'),
                'start_time' => date('Y-m-d H:i:s'),
                'mesin' => $request->get('mesin'),
                'part_name' => $request->get('part_name'),
                'part_type' => $request->get('part_type'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'molding' => $request->get('molding'),
                'material_number' => $request->get('material_number'),
                'dryer' => $request->get('dryer'),
                'dryer_lot_number' => $request->get('dryer_lot_number'),
                'dryer_color' => $request->get('dryer_color'),
                'created_by' => $id_user
            ]);

            $machinework = InjectionMachineWork::where('mesin',$request->get('mesin'))->where('tag_molding',null)->first();
            if (count($machinework) > 0) {
                $machinework->part_name = $request->get('part_name');
                $machinework->part_type = $request->get('part_type');
                $machinework->color = $request->get('color');
                $machinework->cavity = $request->get('cavity');
                $machinework->molding = $request->get('molding');
                $machinework->start_time = $request->get('start_time');
                $machinework->tag_molding = $request->get('tag_molding');
                $machinework->material_number = $request->get('material_number');
                $machinework->dryer = $request->get('dryer');
                $machinework->dryer_lot_number = $request->get('dryer_lot_number');
                $machinework->dryer_color = $request->get('dryer_color');
                $machinework->status = 'Working';
                $machinework->created_by = $id_user;
                $machinework->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Memulai Proses',
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function update_tag(Request $request)
    {
        try {
            $id_user = Auth::id();

            $tag = InjectionTag::where('tag',$request->get('tag'))->first();
            $tag->part_name = $request->get('part_name');
            $tag->operator_id = $request->get('operator_id');
            $tag->part_type = $request->get('part_type');
            $tag->color = $request->get('color');
            $tag->cavity = $request->get('cavity');
            $tag->location = $request->get('location');
            $tag->material_number = $request->get('material_number');
            $tag->availability = 1;
            $tag->save();

            $machinestatus = InjectionMachineMaster::where('mesin',$request->get('location'))->first();
            $machinestatus->status = 'Work';
            $machinestatus->save();

            $response = array(
                'status' => true,
                'message' => 'Tag Updated',
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputReasonIdleTrouble(Request $request)
    {
        try {
            $reason = InjectionMachineWorkingLog::create([
                'mesin' => $request->get('mesin'),
                'status' => $request->get('status'),
                'reason' => $request->get('reason'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'molding' => $request->get('molding'),
                'material_number' => $request->get('material_number'),
                'dryer' => $request->get('dryer'),
                'dryer_lot_number' => $request->get('dryer_lot_number'),
                'start_time' => date('Y-m-d H:i:s'),
                'remark' => 'Open',
                'created_by' => Auth::id()
            ]);

            $temp = InjectionMachineWork::where('mesin',$request->get('mesin'))->first();
            $temp->status = $request->get('status');
            $temp->remark = $request->get('reason');
            $temp->save();

            $response = array(
                'status' => true,
                'message' => '',
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function changeReasonIdleTrouble(Request $request)
    {
        try {
            $reason = InjectionMachineWorkingLog::where('mesin',$request->get('mesin'))->where('remark','Open')->first();
            $reason->end_time = date('Y-m-d H:i:s');
            $reason->remark = 'Close';
            $reason->save();

            $temp = InjectionMachineWork::where('mesin',$request->get('mesin'))->first();
            $temp->status = 'Working';
            $temp->remark = null;
            $temp->save();

            $response = array(
                'status' => true,
                'message' => '',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function get_temp(Request $request){
            // $tgl = $request->get('tgl');
        // $tag_product = $request->get('tag_product');
        // $tag_molding = $request->get('tag_molding');

        $temp = InjectionProcessTemp::where('mesin',$request->get('mesin'))->first();
        $temp_machine = InjectionMachineWork::where('mesin',$request->get('mesin'))->where('tag_molding','!=',null)->first();

        if (count($temp_machine) > 0) {
            $response = array(
                'status' => true,
                'datas' => $temp,
                'data_mesin' => $temp_machine,
                'message' => 'Success get Temp'
            );
            return Response::json($response);
        }else{
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function update_temp(Request $request)
    {
        try {
            $id_user = Auth::id();

            $temp = InjectionProcessTemp::where('mesin',$request->get('mesin'))
            ->where('tag_molding',$request->get('tag_molding'))
            ->first();

            if ($request->get('running_shot') == "") {
                if ($request->get('shot') != $temp->shot) {
                    $shot = $temp->shot;
                    $total = $request->get('shot');
                    $temp->shot = $total;
                }else{
                    $total = $temp->shot;
                }
            }else{
                $shot = $temp->shot;
                $total = $request->get('shot')+$shot;
                $temp->shot = $total;
            }

            $temp->ng_name = $request->get('ng_name');
            $temp->ng_count = $request->get('ng_count');
            $temp->save();

            $response = array(
                'status' => true,
                'total_shot' => $total,
                'message' => 'Temp Updated',
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function create_log(Request $request)
    {
        try {
            $id_user = Auth::id();
            $injection = InjectionProcessLog::create([
                'tag_product' => $request->get('tag_product'),
                'tag_molding' => $request->get('tag_molding'),
                'operator_id' => $request->get('operator_id'),
                'start_time' => $request->get('start_time'),
                'end_time' => date('Y-m-d H:i:s'),
                'mesin' => $request->get('mesin'),
                'part_name' => $request->get('part_name'),
                'part_type' => $request->get('part_type'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'molding' => $request->get('molding'),
                'shot' => $request->get('shot'),
                'material_number' => $request->get('material_number'),
                'ng_name' => $request->get('ng_name'),
                'ng_count' => $request->get('ng_count'),
                'dryer' => $request->get('dryer'),
                'dryer_lot_number' => $request->get('dryer_lot_number'),
                'dryer_color' => $request->get('dryer_color'),
                'created_by' => $id_user
            ]);

            $tag = InjectionTag::where('tag',$request->get('tag_product'))->first();
            $tag->shot = $request->get('shot');
            $tag->location = $request->get('mesin');
            $tag->part_name = $request->get('part_name');
            $tag->operator_id = $request->get('operator_id');
            $tag->part_type = $request->get('part_type');
            $tag->color = $request->get('color');
            $tag->cavity = $request->get('cavity');
            $tag->material_number = $request->get('material_number');
            $tag->availability = 1;
            $tag->save();

            $temp = InjectionProcessTemp::where('mesin',$request->get('mesin'))->delete();

            $molding_master = InjectionMoldingMaster::where('tag',$request->get('tag_molding'))->first();
            
            $last = $molding_master->last_counter;
            $new = $last + $request->get('shot');
            $molding_master->last_counter = $new;

            $last_ng = $molding_master->ng_count;
            $new_ng = $last_ng + $request->get('ng_counting');
            $molding_master->ng_count = $new_ng;
            
            $molding_master->save();

            $molding_log = InjectionMoldingLog::where('tag_molding',$request->get('tag_molding'))->where('status','Running')->orderBy('id','desc')->first();
            if (count($molding_log) > 0) {
                $total_running_shot = $molding_log->total_running_shot;
                $total = $total_running_shot + $request->get('shot');
                $molding_log->status = 'Close';
                $molding_log->save();
            }else{
                $total = $request->get('shot');
            }

            InjectionMoldingLog::create([
                'tag_molding' => $request->get('tag_molding'),
                'mesin' => $request->get('mesin'),
                'part' => $request->get('molding'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'start_time' => $request->get('start_time'),
                'end_time' => date('Y-m-d H:i:s'),
                'running_shot' => $request->get('shot'),
                'total_running_shot' => $total,
                'ng_name' => $request->get('ng_name'),
                'ng_count' => $request->get('ng_count'),
                'status' => 'Running',
                'status_maintenance' => 'Running',
                'created_by' => $id_user
            ]);

            //send Inventories
            // $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
            // $inventory->quantity = ($inventory->quantity+$request->get('shot'));
            // $inventory->save();

            // //send Inj Inventories
            // $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC11']);
            // $injectionInventory->quantity = ($injectionInventory->quantity+$request->get('shot'));
            // $injectionInventory->save();

            // //Transaction
            // InjectionTransaction::create([
            //     'tag' => $request->get('tag_product'),
            //     'material_number' => $request->get('material_number'),
            //     'location' => 'RC11',
            //     'quantity' => $request->get('shot'),
            //     'status' => 'IN',
            //     'operator_id' =>  $request->get('operator_id'),
            //     'created_by' => $id_user
            // ]);

            //COMPLETION KITTO

            // $material = db::connection('mysql2')->table('materials')
            //     ->where('material_number', '=', $request->get('material_number'))
            //     ->first();

            // $completion = db::connection('mysql2')->table('histories')->insert([
            //         "category" => "completion",
            //         "completion_barcode_number" => "",
            //         "completion_description" => $material->description,
            //         "completion_location" => 'RC11',
            //         "completion_issue_plant" => "8190",
            //         "completion_material_id" => $material->id,
            //         "completion_reference_number" => "",
            //         "lot" => $request->get('shot'),
            //         "synced" => 0,
            //         'user_id' => "1",
            //         'created_at' => date("Y-m-d H:i:s"),
            //         'updated_at' => date("Y-m-d H:i:s")
            //     ]);

            //SEMENTARA
            // $transaction = InjectionTag::where('tag',$request->get('tag_product'))->first();
            // $transaction->location = 'RC91';
            // $transaction->availability = 2;
            // $transaction->height_check = 'Uncheck';
            // $transaction->push_pull_check = 'Uncheck';
            // $transaction->torque_check = 'Uncheck';
            // $transaction->save();

            // $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
            // $inventory->quantity = ($inventory->quantity-$request->get('shot'));
            // $inventory->save();

            // $inventory2 = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC91']);
            // $inventory2->quantity = ($inventory2->quantity+$request->get('shot'));
            // $inventory2->save();

            // //send Inj Inventories
            // $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC11']);
            // $injectionInventory->quantity = ($injectionInventory->quantity-$request->get('shot'));
            // $injectionInventory->save();

            // $injectionInventory2 = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
            // $injectionInventory2->quantity = ($injectionInventory2->quantity+$request->get('shot'));
            // $injectionInventory2->save();

            // InjectionTransaction::create([
            //     'material_number' => $request->get('material_number'),
            //     'location' => 'RC11',
            //     'quantity' => $request->get('shot'),
            //     'status' => 'OUT',
            //     'operator_id' => $request->get('operator_id'),
            //     'created_by' => $id_user
            // ]);

            // InjectionTransaction::create([
            //     'material_number' => $request->get('material_number'),
            //     'location' => 'RC91',
            //     'quantity' => $request->get('shot'),
            //     'status' => 'IN',
            //     'operator_id' => $request->get('operator_id'),
            //     'created_by' => $id_user
            // ]);

            $response = array(
                'status' => true,
                'message' => 'Log Created',
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputMesinLog(Request $request)
    {
        try {
            InjectionMachineLog::create([
                'mesin' => $request->get('mesin'),
                'material_number' => $request->get('material_number'),
                'part_name' => $request->get('part_name'),
                'part_type' => $request->get('part_type'),
                'color' => $request->get('color'),
                'cavity' => $request->get('cavity'),
                'molding' => $request->get('molding'),
                'dryer' =>  $request->get('dryer'),
                'dryer_lot_number' =>  $request->get('dryer_lot_number'),
                'dryer_color' =>  $request->get('dryer_color'),
                'start_time' =>  $request->get('start_time'),
                'end_time' =>  date('Y-m-d H:i:s'),
                'created_by' => Auth::id()
            ]);

            $machinework = InjectionMachineWork::where('mesin',$request->get('mesin'))->first();
            if (count($machinework) > 0) {
                $machinework->tag_molding = null;
                $machinework->part_name = null;
                $machinework->part_type = null;
                $machinework->color = null;
                $machinework->cavity = null;
                $machinework->molding = null;
                $machinework->start_time = null;
                $machinework->tag_molding = null;
                $machinework->material_number = null;
                $machinework->dryer = null;
                $machinework->dryer_lot_number = null;
                $machinework->dryer_color = null;
                $machinework->status = null;
                $machinework->save();
            }

            InjectionProcessTemp::where('mesin',$request->get('mesin'))->forceDelete();
            
            $response = array(
                'status' => true,
                'message' => 'Proses Injeksi Selesai',
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

        // ------------------------------- end operator mesin


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

        // ------------- monhtly report

    public function indexMonhtlyStock()
    {
        return view('injection.monthlyStock')->with('page', 'Injection Monhtly Target')->with('jpn', '???');
    }

    public function MonhtlyStock(Request $request){
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl.'-d';
        $tgl2 = $tgl.'-01';

        $model2 = "AND color like 'MJ%'";

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
            $model2 ="AND color like '%A YRF B%'";
        }else{
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }




        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (                           
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP '".$reg2."'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '".$reg."' ".$model2."
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockHead(Request $request){
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl.'-d';
        $tgl2 = $tgl.'-01';

        $model2 = "AND color like 'HJ%'";

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
            $model2 ="AND color like '%A YRF H%'";
        }else{
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }




        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (                           
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP '".$reg2."'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '".$reg."' ".$model2."
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockAll(Request $request){
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl.'-d';
        $tgl2 = $tgl.'-01';

        $model2 = "AND color like 'FJ%'";

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
            $model2 ="AND color like '%A YRF S%'";
        }else{
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }




        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (                    
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP '".$reg2."'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as mj from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '".$reg."' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as block from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '".$reg."' AND
        color like 'BJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as head from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '".$reg."' AND
        color like 'HJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as foot from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '".$reg."' AND
        color like 'FJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";
        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockAllYrf(Request $request){
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


        $query = "SELECT week_date, SUM(assy) assy, SUM(b) b, SUM(s) s, SUM(h) h from (
        SELECT date.week_date, COALESCE(quantity,0) as assy, 0 as b, 0 as s, 0 as h    from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRF21|YRF21K'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(b,0) b,  0 as s, 0 as h  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as b from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
        color like 'A YRF B%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as b,  COALESCE(s,0) as s, 0 as h  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as s from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
        color like 'A YRF S%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as b,  0 as s, COALESCE(h,0) as h  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as h from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
        color like 'A YRF H%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date
        ) as a GROUP BY week_date
        ";
        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockFoot(Request $request){
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl.'-d';
        $tgl2 = $tgl.'-01';

        $model2 = "AND color like 'FJ%'";

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
            $model2 ="AND color like '%A YRF S%'";
        }else{
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }




        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (                           
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP '".$reg2."'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '".$reg."' ".$model2."
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";
        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function MonhtlyStockBlock(Request $request){
        $tgl = $request->get('tgl');

        $location = $request->get('location');

        $tgl1 = $tgl.'-d';
        $tgl2 = $tgl.'-01';

        $model2 = "AND color like 'BJ%'";

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
            $model2 ="AND color like '%A YRF S%'";
        }else{
            $reg = "YRS20BB|YRS20GB";
            $reg2 = "YRS20BB|YRS20GB|YRS20GBK";
        }




        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (                           
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP '".$reg2."'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP '".$reg."' ".$model2."
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='2019-11-01' and week_date <='2019-11-30'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }




        // ------------- end monhtly report

        // ------------- start daily ng report

    public function indexDailyNG()
    {
        return view('injection.dailyNG')->with('title', 'Daily Injection NG Monitoring')->with('title_jp', '???');
    }

    public function dailyNG(Request $request){
            // $tgl = $request->get('tgl');
        if($request->get('tgl') == ''){
            $tgl = date('Y-m-d');
        }
        else{
            $tgl = $request->get('tgl');
        }

        $query = "SELECT IF(mesin = 'MESIN1',
        'Mesin 1',
        IF(mesin = 'MESIN2',
        'Mesin 2',
        IF(mesin = 'MESIN3',
        'Mesin 3',
        IF(mesin = 'MESIN4',
        'Mesin 4',
        IF(mesin = 'MESIN5',
        'Mesin 5',
        IF(mesin = 'MESIN6',
        'Mesin 6',
        IF(mesin = 'MESIN7',
        'Mesin 7',
        IF(mesin = 'MESIN8',
        'Mesin 8',
        IF(mesin = 'MESIN9',
        'Mesin 9',
        IF(mesin = 'MESIN11',
        'Mesin 11',
        0))))))))))
        as mesin, 
        IF(mesin = 'MESIN1',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 1'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN2',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 2'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN3',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 3'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN4',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 4'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN5',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 5'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN6',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 6'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN7',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 7'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN8',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 8'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN9',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 9'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN11',
        COALESCE((select SUM(SUM_OF_LIST(ng_count))
        from ng_log_mesin_injections 
        where mesin = 'Mesin 11'
        and DATE(created_at) = '".$tgl."'),0),0))))))))))
        as jumlah_ng,
        IF(mesin = 'MESIN1',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 1'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN2',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 2'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN3',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 3'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN4',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 4'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN5',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 5'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN6',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 6'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN7',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 7'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN8',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 8'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN9',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 9'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN11',
        COALESCE((select SUM(jumlah_shot)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 11'
        and DATE(created_at) = '".$tgl."'),0),0))))))))))
        as jumlah_shot,
        IF(mesin = 'MESIN1',
        COALESCE((select (SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100
        from ng_log_mesin_injections 
        where mesin = 'Mesin 1'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN2',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 2'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN3',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 3'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN4',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 4'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN5',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 5'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN6',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 6'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN7',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 7'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN8',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 8'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN9',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 9'
        and DATE(created_at) = '".$tgl."'),0),
        IF(mesin = 'MESIN11',
        COALESCE((select ROUND((SUM(SUM_OF_LIST(ng_count))/SUM(jumlah_shot))*100,2)
        from ng_log_mesin_injections 
        where mesin = 'Mesin 11'
        and DATE(created_at) = '".$tgl."'),0),0))))))))))
        as persen_ng
        FROM `status_mesin_injections`
        ";


        $dailyNG = DB::select($query);
        $response = array(
            'status' => true,            
            'datas' => $dailyNG
        );
        return Response::json($response);
    }

    public function detailDailyNG(Request $request)
    {
            // if($request->get('tgl') == ''){
            //     $tanggal = date('Y-m-d');
            // }
            // else{
        $tanggal = $request->get('tanggal');
            // }
        $mesin = $request->get("mesin");

        $query = "select * from ng_log_mesin_injections where mesin = '".$mesin."' and date(created_at) = '".$tanggal."'";

        $detail = db::select($query);

        $response = array(
          'status' => true,
          'lists' => $detail,
      );
        return Response::json($response);
    }

        // ------------- end daily ng report

        // ------------- start molding monitoring

    public function index_molding_monitoring()
    {
        return view('injection.molding_monitoring')->with('title', 'Molding Maintenance Monitoring')->with('title_jp', '金型保全管理');
    }

    public function molding_monitoring(Request $request){
            // $tgl = $request->get('tgl');
            // if($request->get('tgl') == ''){
            //     $tgl = date('Y-m-d');
            // }
            // else{
            //     $tgl = $request->get('tgl');
            // }

        $query_pasang = DB::SELECT("SELECT * FROM `injection_molding_masters` where remark = 'RC' and status = 'PASANG' order by COALESCE(SUBSTR(status_mesin,LENGTH(SUBSTR(status_mesin,1,7)),8),0)");

        $query_ready = DB::SELECT("SELECT * FROM `injection_molding_masters` where remark = 'RC' and status = 'LEPAS' order by COALESCE(SUBSTR(status_mesin,LENGTH(SUBSTR(status_mesin,1,7)),8),0)");
        $query_not_ready = DB::SELECT("SELECT
            * 
        FROM
            `injection_molding_masters` 
        WHERE
        ( remark = 'RC' AND status = 'DIPERBAIKI' ) 
        OR
            ( remark = 'RC' AND status = 'HARUS MAINTENANCE' ) 
            OR (
                remark = 'RC' 
            AND status = 'LEPAS' 
            AND last_counter >= 15000)");
        $query_maintenance = DB::SELECT("SELECT
            * 
        FROM
            `injection_molding_masters` 
        WHERE
            remark = 'RC' AND status = 'DIPERBAIKI'");
        $response = array(
            'status' => true,            
            'query_pasang' => $query_pasang,
            'query_ready' => $query_ready,
            'query_not_ready' => $query_not_ready,
            'query_maintenance' => $query_maintenance,
        );
        return Response::json($response);
    }

        // ------------- end molding monitoring

        // -------------------- start persen mesin

    public function chartWorkingMachine(Request $request){

        $query = "SELECT week_date, SUM(total_1) total_1, SUM(total_2) total_2, SUM(total_3)total_3, SUM(total_4) total_4, SUM(total_5) total_5, SUM(total_6) total_6, SUM(total_7) total_7, SUM(total_8) total_8, SUM(total_9) total_9, SUM(total_11) total_11 from (
        SELECT mesin,week_date, SUM(qty) as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 1'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, SUM(qty) as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 2'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, 0 as total_2, SUM(qty) as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 3'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, SUM(qty) as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 4'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, SUM(qty) as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 5'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, SUM(qty) as total_6, 0 as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 6'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, SUM(qty) as total_7, 0 as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 7'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, SUM(qty) as total_8, 0 as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 8'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, SUM(qty) as total_9, 0 as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 9'
        GROUP BY week_date,mesin 

        UNION all

        SELECT mesin,week_date, 0 as total_1, 0 as total_2, 0 as total_3, 0 as total_4, 0 as total_5, 0 as total_6, 0 as total_7, 0 as total_8, 0 as total_9, SUM(qty) as total_11 from plan_mesin_injection_tmps 
        LEFT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and DATE_FORMAT(week_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d') <='2019-11-31'
        ) as date on plan_mesin_injection_tmps.due_date = date.week_date
        WHERE mesin='Mesin 11'
        GROUP BY week_date,mesin 
        ) as total GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function percenMesin(Request $request){

        $query = "SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 1' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 2' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 3' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 4' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 5' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 6' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 7' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 8' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 9' GROUP BY due_date,mesin
        ) a GROUP BY mesin

        union all

        SELECT mesin,COUNT(IF(OFF = '1', 1, NULL)) 'OFF', COUNT(IF(OFF = '0', 1, NULL)) 'ON' from (
        SELECT mesin,COUNT(IF(color = 'OFF', 1, NULL)) 'OFF' from plan_mesin_injection_tmps WHERE mesin ='Mesin 11' GROUP BY due_date,mesin
        ) a GROUP BY mesin
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

         // -------------------- end persen mesin

        // -------------------- start mj mesin

    public function detailPartMJBlue(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (                           
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND color like 'MJ%'
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

         // -------------------- end mj mesin

         // -------------------- start hj mesin

    public function detailPartHeadBlue(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (                           
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND color like 'HJ%'
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyGreen(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (                    
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BG|YRS20GG|YRS20GGK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as mj from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as block from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
        color like 'BJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as head from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
        color like 'HJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as foot from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BG|YRS20GG' AND
        color like 'FJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyBlue(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (                    
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as mj from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as block from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
        color like 'BJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as head from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
        color like 'HJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as foot from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND
        color like 'FJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyPink(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (                    
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BP|YRS20GP|YRS20GPK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as mj from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as block from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
        color like 'BJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as head from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
        color like 'HJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as foot from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BP|YRS20GP' AND
        color like 'FJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyRed(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (                    
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BR'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as mj from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as block from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
        color like 'BJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as head from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
        color like 'HJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as foot from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BR' AND
        color like 'FJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyBrown(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (                    
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS24BUK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as mj from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as block from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
        color like 'BJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as head from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
        color like 'HJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as foot from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS24BUK' AND
        color like 'FJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyIvory(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(mj) mj, SUM(block) block,SUM(head) head, SUM(foot) foot FROM (                    
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as mj, 0 as block, 0 as head, 0 as foot     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS23|YRS23BR|YRS23CA|YRS23K|YRS27III|YRS24B|YRS24BBR|YRS24BCA|YRS24BK|YRS28BIII'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(mj,0) mj,  0 as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as mj from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
        color like 'MJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  COALESCE(block,0) as block, 0 as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as block from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
        color like 'BJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, COALESCE(head,0) as head, 0 as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as head from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
        color like 'HJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as mj,  0 as block, 0 as head, COALESCE(foot,0) as foot  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as foot from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS23|YRS24B MIDDLE' AND
        color like 'FJ%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function injeksiVsAssyYrf(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(assy) assy, SUM(b) b, SUM(s) s, SUM(h) h from (
        SELECT date.week_date, COALESCE(quantity,0) as assy, 0 as b, 0 as s, 0 as h    from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRF21|YRF21K'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(b,0) b,  0 as s, 0 as h  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as b from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
        color like 'A YRF B%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as b,  COALESCE(s,0) as s, 0 as h  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as s from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
        color like 'A YRF S%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, 0 as b,  0 as s, COALESCE(h,0) as h  FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as h from (
        SELECT due_date, color, qty from plan_mesin_injections WHERE part REGEXP 'YRF21' AND
        color like 'A YRF H%' 
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date
        ) as a GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

         // -------------------- end hj mesin

        // -------------------- start fj mesin

    public function detailPartFootBlue(Request $request){
        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (                           
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND color like 'FJ%'
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

         // -------------------- end fj mesin

        // -------------------- start fj mesin

    public function detailPartBlockBlue(Request $request){

        $from = $request->get('from');
        $to = $request->get('toa');


        $query = "SELECT week_date, SUM(ASSY) assy, SUM(target) target FROM (                           
        SELECT date.week_date, COALESCE(quantity,0) as ASSY, 0 as target     from (
        SELECT target.due_date,target.quantity  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='".$from."' and DATE_FORMAT(due_date,'%Y-%m-%d') <='".$to."'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        WHERE model REGEXP 'YRS20BB|YRS20GB|YRS20GBK'
        ) target
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on target.due_date = date.week_date

        union all

        SELECT week_date , 0 as assy, COALESCE(target,0) target FROM (                                       
        SELECT * from (
        select a.*, SUM(qty) as target from (
        SELECT due_date, color, qty from plan_mesin_injection_tmps WHERE part REGEXP 'YRS20BB|YRS20GB' AND color like 'BJ%'
        ) a GROUP BY due_date   
        ) target                                                                                                         
        ) as aa
        RIGHT JOIN (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) and week_date >='".$from."' and week_date <='".$to."'
        ) as date on aa.due_date = date.week_date

        ) TARGET GROUP BY week_date
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

         // -------------------- end fj mesin

        // ---------------------- master working machine

    public function masterMachine(){
        $mesin = $this->mesin;
        $color = $this->color;
        $part = $this->part;
        $model = $this->model;

        return view('injection.masterMachine',array(        
            'mesin' => $mesin,
            'color' => $color,
            'part' => $part,
            'model' => $model,
        ))->with('page', 'Machine Injection')->with('jpn', '???');

    }

    public function fillMasterMachine(Request $request)
    {
        $op = "select id,mesin,part,color,model from working_mesin_injections";
        $ops = DB::select($op);
        return DataTables::of($ops)

        ->addColumn('edit', function($ops){
            return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
        })
        ->rawColumns(['edit' => 'edit'])

        ->make(true);
    }

    public function editMasterMachine(Request $request)
    {
        $id_op = WorkingMesinInjection::where('id', '=', $request->get('id'))->get();

        $response = array(
            'status' => true,
            'id_op' => $id_op,          
        );
        return Response::json($response);
    }

    public function updateMasterMachine(Request $request){
        $id_user = Auth::id();

        try {  
            $op = WorkingMesinInjection::where('id','=', $request->get('id'))       
            ->first(); 
            $op->mesin = $request->get('mesin');
            $op->part = $request->get('part');
            $op->color = $request->get('color');
            $op->model = $request->get('model');
            $op->created_by = $id_user;

            $op->save();

            $response = array(
              'status' => true,
              'message' => 'Update Success',
          );
            return redirect('/index/masterMachine')->with('status', 'Update Machine success')->with('page', 'Master Operator');
        }catch (QueryException $e){
            return redirect('/index/masterMachine')->with('error', $e->getMessage())->with('page', 'Master Operator');
        }

    }

    public function addMasterMachine(Request $request){
        $id_user = Auth::id();

        try { 

            $head = new WorkingMesinInjection([
                'mesin' => $request->get('mesin3'),
                'part' => $request->get('part3'),
                'color' => $request->get('color3'),
                'model' => $request->get('model3'),
                'qty' => '1',
                'created_by' => $id_user
            ]);
            $head->save();



            $response = array(
              'status' => true,
              'message' => 'Add Machine Success',
          );
            return redirect('/index/masterMachine')->with('status', 'Update Machine success')->with('page', 'Master Operator');
        }catch (QueryException $e){
            return redirect('/index/masterMachine')->with('error', $e->getMessage())->with('page', 'Master Operator');
        }

    }

    public function chartMasterMachine(Request $request){

        $query = "SELECT a.*, CONVERT(SPLIT_STRING(mesin,'N',2),SIGNED INTEGER) as a FROM (
        SELECT mesin, COUNT(part) as working from working_mesin_injections GROUP BY mesin 
        )a ORDER BY a
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }



        // ---------------- end

        // ---------------------- master cycle machine

    public function masterCycleMachine(){
        $mesin = $this->mesin;
        $color = $this->color;
        $part = $this->part;
        $model = $this->model;

        return view('injection.masterCycleMachine',array(        
            'mesin' => $mesin,
            'color' => $color,
            'part' => $part,
            'model' => $model,
        ))->with('page', 'Cycle Machine Injection')->with('jpn', '???');

    }

    public function fillMasterCycleMachine(Request $request)
    {
        $op = "select id,part,model,cycle,shoot,qty,qty_hako,qty_mesin,color from cycle_time_mesin_injections";
        $ops = DB::select($op);
        return DataTables::of($ops)

        ->addColumn('edit', function($ops){
            return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
        })
        ->rawColumns(['edit' => 'edit'])

        ->make(true);
    }

    public function chartMasterCycleMachine(Request $request){

        $query = "SELECT mesin.*, cycle,shoot,qty_hako from (
        SELECT part,color,COUNT(part) as total from working_mesin_injections
        GROUP BY part,color
        ) as mesin
        LEFT JOIN cycle_time_mesin_injections 
        ON mesin.part = cycle_time_mesin_injections.part and mesin.color = cycle_time_mesin_injections.color
        ORDER BY part
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function workingPartMesin(Request $request){

        $mesin = $request->get('mesin');

        $query = "SELECT part,color,model from working_mesin_injections WHERE mesin='".$mesin."'
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

    public function workingPartMesina(Request $request){

        $mesin = $request->get('mesin');

        $query = "SELECT part,color,model from working_mesin_injections WHERE mesin='".$mesin."'
        ";


        $part = DB::select($query);
        $response = array(
            'status' => true,            
            'part' => $part,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }

        // ------------------- sock 3 hari

    public function indexPlanAll(){

        return view('injection.shedule_3_hari')->with('page', 'Injection')->with('jpn', '???');

    }

    public function getPlanAll(Request $request){

        $mesin = $request->get('mesin');

        $queryfjivory = "SELECT COALESCE(part,'-')part,COALESCE(color,'-')color, COALESCE(quantity,0)quantity, COALESCE(quantity2,0)quantity2, COALESCE(total2,0)total2, COALESCE(total_22,0)total_all,  week_date from (
        SELECT a.*, SUM(total)as total2, SUM(total_2)as total_22, SUM(quantity)as quantity2 from (
        SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, (quantity * 3) as total, (quantity * 2) as total_2  from (
        SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        ) as target_model
        CROSS join  detail_part_injections on target_model.model = detail_part_injections.model                             
        WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d')<='2019-11-30' and DATE_FORMAT(week_date,'%Y')='2019')
        and part_code like 'FJ%' 
        and color ='ivory'
        ORDER BY due_date                     
        ) a GROUP BY  due_date, color
        ) target 
        RIGHT JOIN (
        SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='2019-11-01' and 
        DATE_FORMAT(week_date,'%Y-%m-%d')<='2019-11-30'
        )weekd              
        on target.due_date = weekd.week_date
        union all
        SELECT part,color,qty,qty2,total2,total_all,week_date as due_date from (
        SELECT 0 as part,0 as color,0 as qty,0 as qty2, 0 as total2,0 as total_all ,week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) 
        and week_date <'2019-11-01' ORDER BY week_date desc limit 2

        ) a
        ORDER BY week_date
        ";

        $queryfjSkelton = "SELECT COALESCE(part,'-')part,COALESCE(color,'-')color, COALESCE(quantity,0)quantity, COALESCE(quantity2,0)quantity2, COALESCE(total2,0)total2, COALESCE(total_22,0)total_all, due_date from (
        SELECT a.*, SUM(total)as total2, SUM(quantity)as quantity2,SUM(total_2)as total_22 from (
        SELECT target_model.*,detail_part_injections.part,detail_part_injections.part_code,detail_part_injections.color, (quantity * 3) as total , (quantity * 2) as total_2 from (
        SELECT target.material_number,target.due_date,target.quantity,materials.model  from (
        SELECT material_number,due_date,quantity from production_schedules WHERE 
        material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
        DATE_FORMAT(due_date,'%Y-%m-%d') >='2019-11-01' and DATE_FORMAT(due_date,'%Y-%m-%d') <='2019-11-30'
        ) target
        LEFT JOIN materials on target.material_number = materials.material_number 
        ) as target_model
        CROSS join  detail_part_injections on target_model.model = detail_part_injections.model                             
        WHERE due_date in ( SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d')<='2019-11-30' and DATE_FORMAT(week_date,'%Y')='2019')
        and part_code like 'FJ%' 
        and color ='ivory'
        ORDER BY due_date                     
        ) a GROUP BY  due_date, color
        ) target 
        union all
        SELECT part,color,qty,qty2,total2,total3,week_date as due_date from (
        SELECT 0 as part,0 as color,0 as qty,0 as qty2, 0 as total2 , 0 as total3,week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) 
        and week_date <'2019-11-01' ORDER BY week_date desc limit 2

        ) a
        ORDER BY due_date
        ";

        $query2 = "SELECT * from (
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) 
        and week_date <='2019-11-01' ORDER BY week_date desc limit 3
        ) a
        union all
        SELECT week_date from ympimis.weekly_calendars WHERE 
        week_date not in ( SELECT tanggal from  ftm.kalender) 
        and week_date >='2019-11-01' and week_date <='2019-11-31' GROUP BY week_date 
        ORDER BY week_date
        ";


        $queryfjivory = DB::select($queryfjSkelton);
        $FJSkelton = DB::select($queryfjSkelton);

        $tgl = DB::select($query2);
        $response = array(
            'status' => true,            
            'partFJI' => $queryfjivory,

            'FJSkelton' => $FJSkelton,            
            'tgl' => $tgl,
            'message' => 'Get Part Success',
        );
        return Response::json($response);
    }


    public function molding()
    {
        $title = 'Molding Setup';
        $title_jp = '金型設定';
        $molding = InjectionMoldingLog::where('status_maintenance','Running')->get();
        return view('injection.molding', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'molding' => $molding,
            'mesin' => $this->mesin,
            'name' => Auth::user()->name
        ))->with('page', 'Molding Setup');
    }

    public function get_molding(Request $request){
            // $tgl = $request->get('tgl');
            $mesin = $request->get('mesin');

        $molding = DB::SELECT("SELECT
                injection_molding_masters.part,
                injection_molding_masters.product,
                status_mesin AS mesin,
                ( SELECT pic FROM injection_history_molding_logs WHERE injection_history_molding_logs.mesin = status_mesin ORDER BY created_at DESC LIMIT 1 ) AS pic,
                COALESCE ( injection_molding_masters.last_counter / injection_molding_masters.qty_shot, 0 ) AS shot 
            FROM
                injection_molding_masters
            WHERE
                remark = 'RC' 
                AND injection_molding_masters.STATUS = 'PASANG' 
                AND status_mesin = '".$mesin."'");

        $response = array(
            'status' => true,            
            'datas' => $molding,
            'message' => 'Success get Molding Log'
        );
        return Response::json($response);
    }

    public function get_molding_pasang(Request $request){
            // $tgl = $request->get('tgl');
        $mesin = substr($request->get('mesin'),6);


        $molding = InjectionMoldingMaster::where('status','LEPAS')->
        where('mesin','like','%'.$mesin.'%')->
        where('remark','=','RC')->get();

        $molding2 = InjectionMoldingMaster::where('status','PASANG')->where('mesin','like','%'.$mesin.'%')->get();

        $pesan = '';
        foreach ($molding2 as $key) {
            if ($key->status_mesin == $request->get('mesin')) {
                $pesan = $request->get('mesin').' Sudah Terpasang Molding!';
            }
            else{
                $pesan = '';
            }
        }

        $response = array(
            'status' => true,            
            'datas' => $molding,
            'pesan' => $pesan,
            'message' => 'Success get Molding Log Pasang'
        );
        return Response::json($response);
    }

    public function fetch_molding(Request $request){
            // $tgl = $request->get('tgl');
        $id = $request->get('id');

        $molding = InjectionMoldingLog::find($id);

        $response = array(
            'status' => true,            
            'datas' => $molding,
                // 'message' => 'Success get Molding Log'
        );
        return Response::json($response);
    }

    public function fetch_molding_pasang(Request $request){
            // $tgl = $request->get('tgl');
        $id = $request->get('id');

        $molding = InjectionMoldingMaster::find($id);

        $response = array(
            'status' => true,            
            'datas' => $molding,
                // 'message' => 'Success get Molding Log'
        );
        return Response::json($response);
    }

    function store_history_temp(Request $request)
    {
        try{    
          $id_user = Auth::id();

          InjectionHistoryMoldingTemp::create([
            'type' => $request->get('type'),
            'pic' => $request->get('pic'),
            'mesin' => $request->get('mesin'),
            'part' => $request->get('part'),
            'color' => $request->get('color'),
            'total_shot' => $request->get('total_shot'),
            'start_time' => $request->get('start_time'),
            'created_by' => $id_user
        ]);

          $molding = InjectionMoldingLog::where('mesin',$request->get('mesin'))->where('part',$request->get('part'))->where('color',$request->get('color'))->where('status_maintenance','Running')->get();

          if(count($molding) == 0){

          }else{
              foreach ($molding as $molding) {
               $id_molding = $molding->id;
               $molding2 = InjectionMoldingLog::find($id_molding);
               $molding2->status_maintenance = 'Maintenance';
               $molding2->save();
           }
       }

       $response = array(
        'status' => true,
        'start_time' => $request->get('start_time')
    );
                  // return redirect('index/interview/details/'.$interview_id)
                  // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
       return Response::json($response);
    }catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
    );
      return Response::json($response);
    }
    }

    public function get_history_temp(Request $request){
            // $tgl = $request->get('tgl');
        $mesin = $request->get('mesin');

        $molding = InjectionHistoryMoldingTemp::where('mesin',$mesin)->get();

        $response = array(
            'status' => true,            
            'datas' => $molding,
            'message' => 'Success get History Temp'
        );
        return Response::json($response);
    }

    public function update_history_temp(Request $request){
            // $tgl = $request->get('tgl');
        $mesin = $request->get('mesin');
        $type = $request->get('type');

        $history_temp = InjectionHistoryMoldingTemp::where('mesin',$mesin)->where('type',$type)->get();
        foreach ($history_temp as $key) {
            $id_history_temp = $key->id;
        }
        $history_temp2 = InjectionHistoryMoldingTemp::find($id_history_temp);
        $history_temp2->note = $request->get('note');
        $history_temp2->save();

        $response = array(
            'status' => true,
            'message' => 'Success Update Temp'
        );
        return Response::json($response);
    }

    public function cancel_history_molding(Request $request)
    {
        try {
            InjectionHistoryMoldingTemp::where('mesin',$request->get('mesin'))->where('type',$request->get('type'))->delete();

            if ($request->get('type') == 'LEPAS') {
                $molding_master = InjectionMoldingLog::where('part',$request->get('part'))->where('status_maintenance',"Maintenance")->get();
                foreach ($molding_master as $molding_master) {
                     $id_molding_master = $molding_master->id;
                     $molding3 = InjectionMoldingLog::find($id_molding_master);
                     $molding3->status_maintenance = "Running";
                     $molding3->save();
                }
            }

            $response = array(
                'status' => true
            );
            return Response::json($response);

        }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    function store_history_molding(Request $request)
    {
        try{    
          $id_user = Auth::id();
          $start_time = $request->get('start_time');
          $end_time = $request->get('end_time');
          $running_time = $request->get('running_time');

          if (date('D')=='Fri') {
                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_jumat_1['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_jumat_1['ist1_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist2_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-40 minutes',strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist3_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
                }
          }else{
                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist1_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist2_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-40 minutes',strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist3_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
                }
          }
          if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist1_end']) {
                $end_time = date('Y-m-d H:i:s',strtotime('-15 minutes',strtotime($end_time)));
            }

            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist2_end']) {
                $end_time = date('Y-m-d H:i:s',strtotime('-25 minutes',strtotime($end_time)));
            }

            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist3_end']) {
                $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
            }

          $timenow = strtotime($end_time) - strtotime($start_time);

          $years = floor($timenow / (365*60*60*24));  
  
            $months = floor(($timenow - $years * 365*60*60*24) 
                                           / (30*60*60*24));  
              
            $days = floor(($timenow - $years * 365*60*60*24 -  
                         $months*30*60*60*24)/ (60*60*24)); 
              
            $hours = floor(($timenow - $years * 365*60*60*24  
                   - $months*30*60*60*24 - $days*60*60*24) 
                                               / (60*60));  
              
            $minutes = floor(($timenow - $years * 365*60*60*24  
                     - $months*30*60*60*24 - $days*60*60*24  
                                      - $hours*60*60)/ 60);  
              
            $seconds = floor(($timenow - $years * 365*60*60*24  
                     - $months*30*60*60*24 - $days*60*60*24 
                            - $hours*60*60 - $minutes*60));  

            $length = 2;

            $running_time = str_pad($hours,$length,"0", STR_PAD_LEFT).':'.str_pad($minutes,$length,"0", STR_PAD_LEFT).':'.str_pad($seconds,$length,"0", STR_PAD_LEFT);

            if ($request->get('pic_2') != '-') {
                $pic = $request->get('pic_1').', '.$request->get('pic_2');
            }

            if ($request->get('pic_3') != '-') {
                $pic = $request->get('pic_1').', '.$request->get('pic_2').', '.$request->get('pic_3');
            }

            if ($request->get('pic_2') == '-' && $request->get('pic_3') == '-') {
                $pic = $request->get('pic_1');
            }

            InjectionHistoryMoldingLog::create([
                'type' => $request->get('type'),
                'pic' => $pic,
                'mesin' => $request->get('mesin'),
                'part' => $request->get('part'),
                'color' => $request->get('color'),
                'total_shot' => $request->get('total_shot'),
                'start_time' => $request->get('start_time'),
                'end_time' => $request->get('end_time'),
                'running_time' => $running_time,
                'note' => $request->get('notelepas'),
                'decision' => $request->get('decision'),
                'created_by' => $id_user
            ]);

          

          InjectionHistoryMoldingTemp::where('mesin',$request->get('mesin'))->delete();

          if ($request->get('type') == 'LEPAS') {
            $molding = InjectionMoldingLog::where('mesin',$request->get('mesin'))->where('part',$request->get('part'))->where('color',$request->get('color'))->where('status','Running')->get();

            if(count($molding) == 0){

            }else{
              foreach ($molding as $molding) {
               $id_molding = $molding->id;
               $molding2 = InjectionMoldingLog::find($id_molding);
               $molding2->status_maintenance = 'Close';
               $molding2->status = 'Close';
               $molding2->save();
           }
       }
       $molding_master = InjectionMoldingMaster::where('part',$request->get('part'))->get();
       foreach ($molding_master as $molding_master) {
         $id_molding_master = $molding_master->id;
         $molding3 = InjectionMoldingMaster::find($id_molding_master);
         if ($request->get('decision') == 'MAINTENANCE') {
             $molding3->status = 'HARUS MAINTENANCE';
         }else{
            $molding3->status = $request->get('reason');
         }
         $molding3->status_mesin = null;
         $molding3->save();
     }
    }

    if ($request->get('type') == 'PASANG') {
        $molding_master = InjectionMoldingMaster::where('part',$request->get('part'))->get();
        foreach ($molding_master as $molding_master) {
         $id_molding_master = $molding_master->id;
         $molding3 = InjectionMoldingMaster::find($id_molding_master);
         $molding3->status = 'PASANG';
         $molding3->status_mesin = $request->get('mesin');
         $molding3->save();
     }
    }

            $response = array(
                'status' => true
            );
            return Response::json($response);
        }catch(\Exception $e){
          $response = array(
            'status' => false,
            'message' => $e->getMessage(),
            );
          return Response::json($response);
        }
    }

    public function molding_maintenance()
    {
        $title = 'Molding Maintenance';
        $title_jp = '金型保全';
            // $molding = MoldingInjectionLog::where('status_maintenance','Running')->get();
        return view('injection.molding_maintenance', array(
            'title' => $title,
            'title_jp' => $title_jp,
                // 'molding' => $molding,
            'username' => Auth::user()->username,
            'name' => Auth::user()->name
        ))->with('page', 'Molding Maintenance');
    }

    public function get_molding_master(Request $request){
            // $tgl = $request->get('tgl');
            // $tag = $request->get('tag');

        $molding = DB::SELECT("SELECT
            injection_molding_masters.id AS id_molding,
            injection_molding_masters.product,
            injection_molding_masters.part,
            COALESCE ( status_mesin, '-' ) AS mesin,
            COALESCE ( injection_molding_logs.color, '-' ) AS color,
            injection_molding_masters.status,
            COALESCE ( ROUND(injection_molding_logs.total_running_shot / injection_machine_cycle_times.shoot,0), 0 ) AS shot
        FROM
            injection_molding_masters
            LEFT JOIN injection_molding_logs ON injection_molding_logs.tag_molding = injection_molding_masters.tag and injection_molding_logs.status = 'Running'
            LEFT JOIN injection_machine_cycle_times ON injection_molding_masters.product = injection_machine_cycle_times.part
            AND injection_molding_logs.color = injection_machine_cycle_times.color 
        WHERE
            remark = 'RC'
        AND injection_molding_masters.status != 'PASANG' ");

        $response = array(
            'status' => true,            
            'datas' => $molding,
            'message' => 'Success get Molding Log'
        );
        return Response::json($response);
    }

    public function fetch_molding_master(Request $request){
            // $tgl = $request->get('tgl');
        $id = $request->get('id');

        $molding = InjectionMoldingMaster::find($id);

        $response = array(
            'status' => true,            
            'datas' => $molding,
                // 'message' => 'Success get Molding Log'
        );
        return Response::json($response);
    }

    function store_maintenance_temp(Request $request)
    {
        try{    
          $id_user = Auth::id();

          InjectionMaintenanceMoldingTemp::create([
            'pic' => $request->get('pic'),
            'mesin' => $request->get('mesin'),
            'part' => $request->get('part'),
            'product' => $request->get('product'),
            'status' => $request->get('status'),
            'last_counter' => $request->get('last_counter'),
            'start_time' => $request->get('start_time'),
            'created_by' => $id_user
        ]);

          $molding = InjectionMoldingMaster::where('part',$request->get('part'))->where('product',$request->get('product'))->get();

          foreach ($molding as $key) {
            $id_molding = $key->id;
            $molding2 = InjectionMoldingMaster::find($id_molding);
            $molding2->status = 'DIPERBAIKI';
            $molding2->save();
        }

        $response = array(
            'status' => true,
            'start_time' => $request->get('start_time')
        );
                  // return redirect('index/interview/details/'.$interview_id)
                  // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
        return Response::json($response);
    }catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
    );
      return Response::json($response);
    }
    }

    public function get_maintenance_temp(Request $request){
            // $tgl = $request->get('tgl');
        $part = $request->get('part');

        $molding = InjectionMaintenanceMoldingTemp::where('part',$part)->get();

        $response = array(
            'status' => true,            
            'datas' => $molding,
            'message' => 'Success get Maintenance Temp'
        );
        return Response::json($response);
    }

    public function update_maintenance_temp(Request $request){
            // $tgl = $request->get('tgl');
        $part = $request->get('part');

        $maintenance_temp = InjectionMaintenanceMoldingTemp::where('part',$part)->get();
        foreach ($maintenance_temp as $key) {
            $id_maintenance_temp = $key->id;
        }
        $maintenance_temp2 = InjectionMaintenanceMoldingTemp::find($id_maintenance_temp);
        $maintenance_temp2->note = $request->get('note');
        $maintenance_temp2->save();

        $response = array(
            'status' => true,
            'message' => 'Success Update Temp'
        );
        return Response::json($response);
    }

    function store_maintenance_molding(Request $request)
    {
        try{    
          $id_user = Auth::id();
          $start_time = $request->get('start_time');
          $end_time = $request->get('end_time');
          $running_time = $request->get('running_time');

          if (date('D')=='Fri') {
                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_jumat_1['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_jumat_1['ist1_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist2_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-40 minutes',strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist3_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
                }
          }else{
                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist1_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist2_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-40 minutes',strtotime($end_time)));
                }

                if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_biasa_1['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_biasa_1['ist3_end']) {
                    $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
                }
          }
          if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist1_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist1_end']) {
                $end_time = date('Y-m-d H:i:s',strtotime('-15 minutes',strtotime($end_time)));
            }

            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist2_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist2_end']) {
                $end_time = date('Y-m-d H:i:s',strtotime('-25 minutes',strtotime($end_time)));
            }

            if (date("H:i:s", strtotime($start_time)) < $this->jam_istirahat_2['ist3_start'] && date("H:i:s", strtotime($end_time)) > $this->jam_istirahat_2['ist3_end']) {
                $end_time = date('Y-m-d H:i:s',strtotime('-10 minutes',strtotime($end_time)));
            }

          $timenow = strtotime($end_time) - strtotime($start_time);

          $years = floor($timenow / (365*60*60*24));  
  
            $months = floor(($timenow - $years * 365*60*60*24) 
                                           / (30*60*60*24));  
              
            $days = floor(($timenow - $years * 365*60*60*24 -  
                         $months*30*60*60*24)/ (60*60*24)); 
              
            $hours = floor(($timenow - $years * 365*60*60*24  
                   - $months*30*60*60*24 - $days*60*60*24) 
                                               / (60*60));  
              
            $minutes = floor(($timenow - $years * 365*60*60*24  
                     - $months*30*60*60*24 - $days*60*60*24  
                                      - $hours*60*60)/ 60);  
              
            $seconds = floor(($timenow - $years * 365*60*60*24  
                     - $months*30*60*60*24 - $days*60*60*24 
                            - $hours*60*60 - $minutes*60));  

            $length = 2;

            $running_time = str_pad($hours,$length,"0", STR_PAD_LEFT).':'.str_pad($minutes,$length,"0", STR_PAD_LEFT).':'.str_pad($seconds,$length,"0", STR_PAD_LEFT);

          InjectionMaintenanceMoldingLog::create([
            'pic' => $request->get('pic'),
            'mesin' => $request->get('mesin'),
            'part' => $request->get('part'),
            'product' => $request->get('product'),
            'status' => $request->get('status'),
            'last_counter' => $request->get('last_counter'),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
            'running_time' => $running_time,
            'note' => $request->get('note'),
            'created_by' => $id_user
        ]);

          $molding = InjectionMoldingMaster::where('part',$request->get('part'))->where('product',$request->get('product'))->get();

          $molding3 = InjectionMaintenanceMoldingTemp::where('part',$request->get('part'))->where('product',$request->get('product'))->delete();

          foreach ($molding as $key) {
            $id_molding = $key->id;
            $molding2 = InjectionMoldingMaster::find($id_molding);
            if ($molding2->status_mesin != null) {
                $molding2->status = 'PASANG';
            }else{
                $molding2->status = 'LEPAS';
                $molding2->last_counter = 0;
                $molding2->ng_count = 0;
            }
            $molding2->save();
        }

        $response = array(
            'status' => true,
            'start_time' => $request->get('start_time')
        );
                  // return redirect('index/interview/details/'.$interview_id)
                  // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
        return Response::json($response);
        }catch(\Exception $e){
          $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
          return Response::json($response);
        }
    }










    // ---------------- end

    public function transaction($status)
    {
        $title = 'Injection Transaction';
        if (strtoupper($status) == 'IN') {
            $title_jp = '成形品の受け渡し（IN）';
        }else{
            $title_jp = '成形品の受け渡し（OUT）';
        }

        return view('injection.transaction_injection', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'status' => strtoupper($status),
            'name' => Auth::user()->name
        ))->with('page', 'Injection Transaction');
    }

    public function scanProduct(Request $request)
    {
        try {
            if ($request->get('status') == "IN") {
                $tag = DB::SELECT("SELECT * FROM `injection_tags` where tag = '".$request->get('tag')."' and location = 'RC11'");
            }else{
                $tag = DB::SELECT("SELECT * FROM `injection_tags` left join injection_process_logs on tag = tag_product and injection_tags.material_number = injection_process_logs.material_number and injection_tags.cavity = injection_process_logs.cavity where tag = '".$request->get('tag')."' and location = 'RC91' and injection_process_logs.remark is null");
            }
            if (count($tag) > 0) {
                $response = array(
                    'status' => true,
                    'data' => $tag
                );
                return Response::json($response);
            }else{
                $response = array(
                    'status' => false,
                );
                return Response::json($response);
            }
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchTransaction(Request $request)
    {
        try {
            if ($request->get('status') == 'IN') {
                $transaction = DB::SELECT("SELECT
                    injection_transactions.id,
                    injection_transactions.tag,
                    injection_transactions.material_number,
                    SUBSTRING_INDEX( injection_parts.part_name, ' ', 1 ) AS part_name,
                    injection_parts.part_name as material_description,
                    injection_parts.part_code,
                    injection_parts.part_type,
                    injection_parts.color,
                    injection_transactions.location,
                    injection_transactions.quantity,
                    injection_transactions.created_at,
                    injection_transactions.status,
                    injection_process_logs.mesin,
                    injection_process_logs.cavity,
                    employee_syncs.employee_id,
                    employee_syncs.`name`,
                    injection_process_logs.updated_at,
                    injection_process_logs.ng_name,
                    injection_process_logs.ng_count 
                FROM
                    injection_transactions
                    LEFT JOIN injection_parts ON injection_transactions.material_number = injection_parts.gmc
                    LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_transactions.tag 
                    AND injection_process_logs.updated_at = injection_transactions.created_at
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_transactions.operator_id 
                WHERE
                    injection_transactions.status = '".$request->get('status')."' 
                    AND injection_transactions.location = 'RC11' 
                    AND DATE( injection_transactions.created_at ) BETWEEN DATE(
                    NOW()) - INTERVAL 3 DAY 
                    AND DATE(
                    NOW()) 
                    AND injection_parts.deleted_at IS NULL 
                ORDER BY
                    injection_transactions.created_at DESC");
            }else{
                $transaction = DB::SELECT("SELECT
                    injection_transactions.id,
                    injection_transactions.tag,
                    injection_transactions.material_number,
                    SUBSTRING_INDEX( injection_parts.part_name, ' ', 1 ) AS part_name,
                    injection_parts.part_name as material_description,
                    injection_parts.part_code,
                    injection_parts.part_type,
                    injection_parts.color,
                    injection_transactions.location,
                    injection_transactions.quantity,
                    injection_transactions.created_at,
                    injection_transactions.status,
                    injection_process_logs.mesin,
                    injection_process_logs.cavity,
                    employee_syncs.employee_id,
                    employee_syncs.`name`,
                    injection_process_logs.updated_at,
                    injection_process_logs.ng_name,
                    injection_process_logs.ng_count 
                FROM
                    injection_transactions
                    LEFT JOIN injection_parts ON injection_transactions.material_number = injection_parts.gmc
                    LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_transactions.tag 
                    AND injection_process_logs.updated_at = injection_transactions.created_at
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_transactions.operator_id 
                WHERE
                    injection_transactions.status = 'IN' 
                    AND injection_transactions.location = 'RC91' 
                    AND DATE( injection_transactions.created_at ) BETWEEN DATE(
                    NOW()) - INTERVAL 3 DAY 
                    AND DATE(
                    NOW()) 
                    AND injection_parts.deleted_at IS NULL 
                ORDER BY
                    injection_transactions.created_at DESC");
            }

            $response = array(
                'status' => true,
                'data' => $transaction
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }   
    }

    public function fetchDetailTransaction(Request $request)
    {
        try {
            $transaction = DB::SELECT("SELECT
                injection_transactions.id,
                injection_transactions.tag,
                injection_transactions.material_number,
                injection_parts.part_name as material_description,
                SUBSTRING_INDEX( injection_parts.part_name, ' ', 1 ) AS part_name,
                injection_parts.part_code,
                injection_parts.part_type,
                injection_parts.color,
                injection_transactions.location,
                injection_transactions.quantity,
                injection_transactions.created_at,
                injection_transactions.status,
                injection_process_logs.mesin,
                injection_process_logs.cavity,
                employee_syncs.employee_id,
                employee_syncs.`name`,
                injection_process_logs.updated_at,
                injection_process_logs.ng_name,
                injection_process_logs.ng_count,
                empambil.employee_id as empidambil,
                empambil.name as nameambil
            FROM
                injection_transactions
                LEFT JOIN injection_parts ON injection_transactions.material_number = injection_parts.gmc
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_transactions.tag 
                AND injection_process_logs.updated_at = injection_transactions.created_at
                LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_process_logs.operator_id 
                LEFT JOIN employee_syncs empambil ON empambil.employee_id = injection_transactions.operator_id
            WHERE
                injection_transactions.status = 'IN' 
                AND injection_transactions.location = 'RC91' 
                AND DATE( injection_transactions.created_at ) BETWEEN DATE(
                NOW()) - INTERVAL 3 DAY 
                AND DATE(
                NOW()) 
                AND injection_parts.deleted_at IS NULL 
                AND injection_transactions.id = '".$request->get('id')."'
            ORDER BY
                injection_transactions.created_at DESC");

            $response = array(
                'status' => true,
                'data' => $transaction
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }   
    }

    public function fetchCheckInjections(Request $request)
    {
        try {
            $operator_id = "";
            if ($request->get('status') == 'IN') {
                $remark = 'antenna_1';
                $transaction = DB::SELECT("SELECT
                    -- ympirfid.injection_lists.tag 
                    *,ympimis.injection_tags.id as injection_id,ympimis.injection_tags.tag as tag_rfid
                FROM
                    ympirfid.injection_lists
                    JOIN ympimis.injection_tags ON ympimis.injection_tags.concat_kanban = ympirfid.injection_lists.tag 
                    JOIN employee_syncs ON ympimis.employee_syncs.employee_id = ympimis.injection_tags.operator_id
                WHERE
                    ympirfid.injection_lists.remark = '".$remark."'
                    AND ympimis.injection_tags.availability = 1");
                $operator_id = "";
            }else{
                $remark = 'antenna_2';
                $transaction = DB::SELECT("SELECT
                    -- ympirfid.injection_lists.tag 
                    *,ympimis.injection_tags.id as injection_id,ympimis.injection_tags.tag as tag_rfid,ympimis.injection_process_logs.id as process_id
                FROM
                    ympirfid.injection_lists
                    JOIN ympimis.injection_tags ON ympimis.injection_tags.concat_kanban = ympirfid.injection_lists.tag 
                    LEFT JOIN  ympimis.injection_process_logs ON ympimis.injection_tags.tag = tag_product and ympimis.injection_tags.material_number = ympimis.injection_process_logs.material_number and ympimis.injection_tags.cavity = ympimis.injection_process_logs.cavity AND ympimis.injection_process_logs.remark is null
                    JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = ympimis.injection_tags.operator_id
                WHERE
                    ympirfid.injection_lists.remark = '".$remark."'
                    AND ympimis.injection_tags.availability = 2");
                $operator_id = DB::SELECT("SELECT * from ympirfid.injection_lists left join ympimis.employee_syncs on ympimis.employee_syncs.employee_id = ympirfid.injection_lists.tag where tag like '%PI%'");
            }

            if ($operator_id == "") {
                $response = array(
                    'status' => true,
                    'data' => $transaction,
                );
            }else{
                $response = array(
                    'status' => true,
                    'data' => $transaction,
                    'operator' => $operator_id
                );
            }
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchCheckNg(Request $request)
    {
        try {
            $ng = DB::SELECT("select * from injection_process_logs where id = '".$request->get('id')."'");

            $response = array(
                'status' => true,
                'data' => $ng
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function completion(Request $request)
    {
        try {
            $id_user = Auth::id();
            if ($request->get('status') == 'IN') {
                $transaction = InjectionTag::where('tag',$request->get('tag'))->first();
                $concat_kanban = $transaction->concat_kanban;
                $transaction->location = 'RC11';
                $transaction->availability = 2;
                // $transaction->height_check = 'Uncheck';
                // $transaction->push_pull_check = 'Uncheck';
                // $transaction->torque_check = 'Uncheck';
                $transaction->save();

                // $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
                // $inventory->quantity = ($inventory->quantity-$request->get('qty'));
                // $inventory->save();

                // $inventory2 = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC91']);
                // $inventory2->quantity = ($inventory2->quantity+$request->get('qty'));
                // $inventory2->save();

                //send Inj Inventories
                // $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC11']);
                // $injectionInventory->quantity = ($injectionInventory->quantity-$request->get('qty'));
                // $injectionInventory->save();

                // $injectionInventory2 = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
                // $injectionInventory2->quantity = ($injectionInventory2->quantity+$request->get('qty'));
                // $injectionInventory2->save();

                // InjectionTransaction::create([
                //     'tag' => $request->get('tag'),
                //     'material_number' => $request->get('material_number'),
                //     'location' => 'RC11',
                //     'quantity' => $request->get('qty'),
                //     'status' => 'OUT',
                //     'operator_id' => $request->get('operator_id'),
                //     'created_by' => $id_user
                // ]);

                // InjectionTransaction::create([
                //     'tag' => $request->get('tag'),
                //     'material_number' => $request->get('material_number'),
                //     'location' => 'RC91',
                //     'quantity' => $request->get('qty'),
                //     'status' => 'IN',
                //     'operator_id' => $request->get('operator_id'),
                //     'created_by' => $id_user
                // ]);

                $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
                $inventory->quantity = ($inventory->quantity+$request->get('qty'));
                $inventory->save();

                //send Inj Inventories
                $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC11']);
                $injectionInventory->quantity = ($injectionInventory->quantity+$request->get('qty'));
                $injectionInventory->save();

                //Transaction
                InjectionTransaction::create([
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'location' => 'RC11',
                    'quantity' => $request->get('qty'),
                    'status' => 'IN',
                    'operator_id' =>  $request->get('operator_id'),
                    'created_by' => $id_user
                ]);

                $deleteInjList = DB::SELECT("DELETE FROM ympirfid.injection_lists where tag = '".$concat_kanban."'");

            }else{
                $transaction = InjectionTag::where('tag',$request->get('tag'))->first();
                // $transaction->operator_id = $request->get('operator_id');
                $transaction->operator_id = null;
                $transaction->part_name = null;
                $transaction->part_type = null;
                $transaction->color = null;
                $transaction->cavity = null;
                $transaction->location = null;
                $transaction->shot = null;
                $transaction->availability = null;
                // $transaction->availability = 3;
                $transaction->height_check = null;
                $transaction->push_pull_check = null;
                $transaction->torque_check = null;
                $transaction->remark = null;
                $concat_kanban = $transaction->concat_kanban;
                $transaction->save();

                $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC11']);
                $inventory->quantity = ($inventory->quantity-$request->get('qty'));
                $inventory->save();

                $inventory2 = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material_number'), 'storage_location' => 'RC91']);
                $inventory2->quantity = ($inventory2->quantity+$request->get('qty'));
                $inventory2->save();

                //send Inj Inventories
                $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC11']);
                $injectionInventory->quantity = ($injectionInventory->quantity-$request->get('qty'));
                $injectionInventory->save();

                $injectionInventory2 = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
                $injectionInventory2->quantity = ($injectionInventory2->quantity+$request->get('qty'));
                $injectionInventory2->save();

                InjectionTransaction::create([
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'location' => 'RC11',
                    'quantity' => $request->get('qty'),
                    'status' => 'OUT',
                    'operator_id' => $request->get('operator_id'),
                    'created_by' => $id_user
                ]);

                InjectionTransaction::create([
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'location' => 'RC91',
                    'quantity' => $request->get('qty'),
                    'status' => 'IN',
                    'operator_id' => $request->get('operator_id'),
                    'created_by' => $id_user
                ]);

                // InjectionTransaction::create([
                //     'tag' => $request->get('tag'),
                //     'material_number' => $request->get('material_number'),
                //     'location' => 'RC91',
                //     'quantity' => $request->get('qty'),
                //     'status' => 'OUT',
                //     'operator_id' => $request->get('operator_id'),
                //     'created_by' => $id_user
                // ]);

                // $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
                // $injectionInventory->quantity = ($injectionInventory->quantity-$request->get('qty'));
                // $injectionInventory->save();

                $process = InjectionProcessLog::where('tag_product',$request->get('tag'))->where('material_number',$request->get('material_number'))->where('cavity',$request->get('cavity'))->where('remark',null)->first();
                $process->remark = 'Close';
                $process->save();

                $deleteInjList = DB::SELECT("DELETE FROM ympirfid.injection_lists where tag = '".$concat_kanban."'");

                // $material = db::connection('mysql2')->table('materials')
                // ->where('material_number', '=', $request->get('material_number'))
                // ->first();

                // $transfer = db::connection('mysql2')->table('histories')->insert([
                //     "category" => "transfer",
                //     "transfer_barcode_number" => "",
                //     "transfer_document_number" => "8190",
                //     "transfer_material_id" => $material->id,
                //     "transfer_issue_location" => 'RC11',
                //     "transfer_issue_plant" => "8190",
                //     "transfer_receive_plant" => "8190",
                //     "transfer_receive_location" => 'RC91',
                //     "transfer_cost_center" => "",
                //     "transfer_gl_account" => "",
                //     "transfer_transaction_code" => "MB1B",
                //     "transfer_movement_type" => "9I3",
                //     "transfer_reason_code" => "",
                //     "lot" => $request->get('qty'),
                //     "synced" => 0,
                //     'user_id' => "1",
                //     'created_at' => date("Y-m-d H:i:s"),
                //     'updated_at' => date("Y-m-d H:i:s")
                // ]);
            }

            $response = array(
                'status' => true
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function cancelCompletion(Request $request)
    {
        try {
            $delete = DB::CONNECTION('rfid')->SELECT("DELETE FROM ympirfid.injection_lists WHERE tag = '".$request->get('concat_kanban')."'");
            $response = array(
                'status' => true
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexMachineMonitoring()
    {
        return view('injection.machine_monitoring')
        ->with('mesin', $this->mesin)
        ->with('title', 'Injection Machine Monitoring')
        ->with('title_jp', '成形機の監視');
    }

    public function fetchMachineMonitoring(Request $request)
    {
        try {
            $id_user = Auth::id();

            $data = DB::SELECT("SELECT
                mesin,
                COALESCE (( SELECT part_name FROM injection_process_temps WHERE mesin = injection_machine_works.mesin AND injection_process_temps.deleted_at IS NULL ), '' ) AS part,
                COALESCE ((
                    SELECT
                        CONCAT( '<br>(', part_type, ' - ', color, ')<br>', cavity ) 
                    FROM
                        injection_process_temps 
                    WHERE
                        mesin = injection_machine_works.mesin 
                        AND injection_process_temps.deleted_at IS NULL 
                        ),
                    '' 
                ) AS type,
                COALESCE (( SELECT shot FROM injection_process_temps WHERE mesin = injection_machine_works.mesin AND injection_process_temps.deleted_at IS NULL ), 0 ) AS shot_mesin,
                COALESCE ((
                    SELECT COALESCE
                        ( ROUND( last_counter / injection_molding_masters.qty_shot ), 0 ) AS shot 
                    FROM
                        injection_molding_masters 
                    WHERE
                        injection_molding_masters.status_mesin = injection_machine_works.mesin 
                        ),
                    0 
                ) AS shot_molding,
                COALESCE (( SELECT part FROM injection_molding_masters WHERE injection_molding_masters.status_mesin = injection_machine_works.mesin ), '-' ) AS molding,
                COALESCE (( SELECT ng_count FROM injection_process_temps WHERE mesin = injection_machine_works.mesin AND injection_process_temps.deleted_at IS NULL ), '' ) AS ng_count,
                COALESCE ((
                    SELECT
                        CONCAT( operator_id, '<br>', employee_syncs.`name` ) 
                    FROM
                        injection_process_temps
                        LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_process_temps.operator_id 
                    WHERE
                        mesin = injection_machine_works.mesin 
                        AND injection_process_temps.deleted_at IS NULL 
                        ),
                    '' 
                ) AS operator,
                status,
                remark
            FROM
                injection_machine_works");


            $response = array(
                'status' => true,
                'data' => $data
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexStockMonitoring()
    {
        $color = DB::SELECT('SELECT DISTINCT(color) FROM `injection_parts`');

        return view('injection.stock_monitoring')
        ->with('mesin', $this->mesin)
        ->with('color', $color)
        ->with('title', 'Injection Stock Monitoring')
        ->with('title_jp', '成形品在庫の監視');
    }

    public function fetchStockMonitoring(Request $request)
    {
        try {
            $id_user = Auth::id();

            if ($request->get('color') == "" || $request->get('color') == "All") {
                $color = "";
            }else{
                $color = "AND TRIM( 
                RIGHT(
                        c.part, 
                        (LENGTH(c.part) - LOCATE('(',c.part)) 
                )) =  '".$request->get('color').")'";
            }

            $j = 4;
            $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
            if (date('D')=='Thu') {
                $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
            }
            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
            foreach ($weekly_calendars as $key) {
                if ($key->week_date == $nextdayplus1) {
                    if ($key->remark == 'H') {
                        $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                    }
                }
            }
            if (date('D')=='Fri' || date('D')=='Sat') {
                $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
            }

            $first = date('Y-m-01');
            $now = date('Y-m-d');

            $data_skeleton = DB::SELECT("SELECT
                c.part,
                TRIM( 
                    RIGHT(
                            c.part, 
                            (LENGTH(c.part) - LOCATE('(',c.part)) 
                    )
                ) as color,
                SUM( c.stock ) AS stock,
                SUM( c.stock_assy ) AS stock_assy,
                SUM( c.plan ) AS plan 
            FROM
                (
                SELECT
                    CONCAT( UPPER( injection_parts.part_code ), ' (', injection_parts.color, ')' ) AS part,
                    COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC11' AND material_number = gmc and injection_inventories.deleted_at is null), 0 ) AS stock,
                    COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC91' AND material_number = gmc and injection_inventories.deleted_at is null ), 0 ) AS stock_assy,
                    0 AS plan 
                FROM
                    injection_parts where remark = 'injection'
                GROUP BY injection_parts.part_code,injection_parts.color,gmc,color  UNION ALL
                SELECT
                    part,
                    0 AS stock,
                    0 AS stock_assy,
                    sum( a.plan )- sum( a.stamp ) AS plan 
                FROM
                    (
                    SELECT
                        CONCAT( part_code, ' (', color, ')' ) AS part,
                        SUM( quantity ) AS plan,
                        0 AS stamp 
                    FROM
                        production_schedules
                        LEFT JOIN materials ON materials.material_number = production_schedules.material_number
                        LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
                    WHERE
                        materials.category = 'FG' 
                        AND materials.origin_group_code = '072' 
                        AND production_schedules.due_date BETWEEN '".$first."' 
                        AND '".$nextdayplus1."' 
                    GROUP BY
                        part_code,color UNION ALL
                    SELECT
                        CONCAT( part_code, ' (', color, ')' ) AS part,
                        0 AS plan,
                        SUM( quantity ) AS stamp 
                    FROM
                        flo_details
                        LEFT JOIN materials ON materials.material_number = flo_details.material_number
                        LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
                    WHERE
                        materials.category = 'FG' 
                        AND materials.origin_group_code = '072' 
                        AND DATE( flo_details.created_at ) BETWEEN '".$first."' 
                        AND '".$now."' 
                    GROUP BY
                        part_code,color
                    ) a 
                GROUP BY
                    a.part 
                ) c 
                where c.part not like '%YRF%' AND
                c.part not like '%IVORY%' AND
                c.part not like '%BEIGE%'
            ".$color."
            GROUP BY
                c.part ORDER BY color");

            
            $data_ivory = DB::SELECT("SELECT
                c.part,
                TRIM( 
                    RIGHT(
                            c.part, 
                            (LENGTH(c.part) - LOCATE('(',c.part)) 
                    )
                ) as color,
                SUM( c.stock ) AS stock,
                SUM( c.stock_assy ) AS stock_assy,
                SUM( c.plan ) AS plan 
            FROM
                (
                SELECT
                    CONCAT( UPPER( injection_parts.part_code ), ' (', injection_parts.color, ')' ) AS part,
                    COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC11' AND material_number = gmc and injection_inventories.deleted_at is null), 0 ) AS stock,
                    COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC91' AND material_number = gmc and injection_inventories.deleted_at is null), 0 ) AS stock_assy,
                    0 AS plan 
                FROM
                    injection_parts where remark = 'injection'
                GROUP BY injection_parts.part_code,injection_parts.color,gmc,color  UNION ALL
                SELECT
                    part,
                    0 AS stock,
                    0 AS stock_assy,
                    sum( a.plan )- sum( a.stamp ) AS plan 
                FROM
                    (
                    SELECT
                        CONCAT( part_code, ' (', color, ')' ) AS part,
                        SUM( quantity ) AS plan,
                        0 AS stamp 
                    FROM
                        production_schedules
                        LEFT JOIN materials ON materials.material_number = production_schedules.material_number
                        LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
                    WHERE
                        materials.category = 'FG' 
                        AND materials.origin_group_code = '072' 
                        AND production_schedules.due_date BETWEEN '".$first."' 
                        AND '".$nextdayplus1."' 
                    GROUP BY
                        part_code,color UNION ALL
                    SELECT
                        CONCAT( part_code, ' (', color, ')' ) AS part,
                        0 AS plan,
                        SUM( quantity ) AS stamp 
                    FROM
                        flo_details
                        LEFT JOIN materials ON materials.material_number = flo_details.material_number
                        LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
                    WHERE
                        materials.category = 'FG' 
                        AND materials.origin_group_code = '072' 
                        AND DATE( flo_details.created_at ) BETWEEN '".$first."' 
                        AND '".$now."' 
                    GROUP BY
                        part_code,color
                    ) a 
                GROUP BY
                    a.part 
                ) c 
                where c.part not like '%PINK%' AND
                c.part not like '%RED%' AND
                c.part not like '%BLUE%' AND
                c.part not like '%BROWN%' AND
                c.part not like '%GREEN%'
            ".$color."
            GROUP BY
                c.part ORDER BY part");

            $datas_skeleton = [];
            $datas_ivory = [];

            foreach ($data_ivory as $key) {
                $datas_ivory[] = array(
                    'part' => $key->part,
                    'color' => $key->color,
                    'stock' => $key->stock,
                    'stock_assy' => $key->stock_assy,
                    'plan' => $key->plan, );
            }

            foreach ($data_skeleton as $key) {
                $datas_skeleton[] = array(
                    'part' => $key->part,
                    'color' => $key->color,
                    'stock' => $key->stock,
                    'stock_assy' => $key->stock_assy,
                    'plan' => $key->plan, );
            }

            $response = array(
                'status' => true,
                'datas_skeleton' => $datas_skeleton,
                'datas_ivory' => $datas_ivory,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexDryerResin()
    {
        $dryer = DB::SELECT('select * from injection_dryers');

        return view('injection.index_dryer')
        ->with('mesin', $this->mesin)
        ->with('dryer', $dryer)
        ->with('title', 'Injection Dryer')
        ->with('title_jp', '成形乾燥機');
    }

    public function fetchListResin(Request $request)
    {
        try {
            $id_user = Auth::id();

            $data = DB::SELECT("Select * from injection_parts where remark = 'abs'");

            $response = array(
                'status' => true,
                'datas' => $data
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchResumeResin(Request $request)
    {
        try {
            $id_user = Auth::id();

            $data = DB::SELECT("SELECT
                *,
                injection_dryer_logs.created_at AS created,
                DATE(NOW()) as now,
                DATE(NOW()) - INTERVAL 7 DAY as week_ago
            FROM
                `injection_dryer_logs`
                LEFT JOIN employee_syncs ON employee_syncs.employee_id = injection_dryer_logs.employee_id
            WHERE 
                DATE(injection_dryer_logs.created_at) BETWEEN DATE(NOW()) - INTERVAL 7 DAY and DATE(NOW())
            ORDER BY
                injection_dryer_logs.created_at DESC");

            $response = array(
                'status' => true,
                'datas' => $data
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputResin(Request $request)
    {
        try {
            $id_user = Auth::id();

            $resin = InjectionDryerLog::create([
                'dryer' => $request->get('dryer'),
                'material_number' => $request->get('material_number'),
                'material_description' => $request->get('material_description'),
                'color' => $request->get('color'),
                'qty' => $request->get('qty'),
                'lot_number' => $request->get('lot_number'),
                'type' => 'IN',
                'employee_id' => $request->get('employee_id'),
                'created_by' => $id_user,
            ]);

            $dryer = InjectionDryer::firstOrNew(['dryer' => $request->get('dryer')]);
            $dryer->material_number = $request->get('material_number');
            $dryer->material_description = $request->get('material_description');
            $dryer->color = $request->get('color');

            $dryer->lot_number = $request->get('lot_number');
            $dryer->qty = $request->get('qty');
            $dryer->created_by = $id_user;
            $dryer->save();

            $resin = InjectionResin::create([
                'qty' => $request->get('qty'),
                'lot_number' => $request->get('lot_number'),
                'created_by' => $id_user,
            ]);

            $response = array(
                'status' => true,
                'message' => 'Input Resin Success'
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchDryer(Request $request)
    {
        try {
            if ($request->get('dryer') != null) {
                $dryer = InjectionDryer::where('dryer',$request->get('dryer'))->first();
            }elseif ($request->get('machine') != null) {
                $dryer = InjectionDryer::where('machine',$request->get('machine'))->first();
            }

            if (count($dryer) > 0) {
                $response = array(
                    'status' => true,
                    'dryer' => $dryer,
                );
                return Response::json($response);
            }else{
                $response = array(
                    'status' => false,
                );
                return Response::json($response);
            }
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateDryer(Request $request)
    {
        try {
            $dryer_all = InjectionDryer::get();
            $machines = [];
            foreach ($dryer_all as $key) {
                $machines[] = $key->machine;
            }
            if (in_array($request->get('machine'), $machines)) {
                $status = false;
                $message = 'Mesin sudah terpakai';
            }else{
                $dryer = InjectionDryer::find($request->get('id'));
                $dryer->machine = $request->get('machine');
                $dryer->save();

                $status = true;
                $message = 'Adjustment Success';
            }

            $response = array(
                'status' => $status,
                'message' => $message
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexInjectionSchedule()
    {
        $title = 'Injection Schedule';
        $title_jp = '???';
        return view('injection.schedule_view',array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Injection Schedule View')->with('jpn', '???');
    }

    public function fetchInjectionSchedule()
    {
        $last = date('Y-m-t');
        $first = date('Y-m-01');

        // $schedule = [];

        // for ($i = 0; $i < count($this->mesin); $i++) {
            $schedule = db::select("select * from injection_schedule_logs where date(start_time) >= '".$first."' and date(end_time) <= '".$last."'");
        // }


        // $sch2 = db::select("SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d')>='2019-11-01' and DATE_FORMAT(week_date,'%Y-%m-%d')<='2019-11-31'");

        

        $response = array(
            'status' => true,            
            'schedule' => $schedule,          
            'mesin' => $this->mesin, 
            'first' => $first,
            'last' => $last

        );
        return Response::json($response);

    }

    public function indexInputStock()
    {
        $materials = DB::SELECT("SELECT *,gmc as material_number, part_name as material_description FROM `injection_parts` where remark = 'injection' and deleted_at is null");

        $title = 'Input Daily Stock Recorder';
        $title_jp = '???';
        return view('injection.input_stock',array(
            'title' => $title,
            'title_jp' => $title_jp,
            'materials' => $materials,
        ))->with('page', 'Input Daily Stock Recorder')->with('jpn', '???');
    }

    public function inputStock(Request $request)
    {
        try {
            $id_user = Auth::id();

            $injection_inventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material_number'), 'location' => 'RC91']);
            $injection_inventory->quantity = $request->get('quantity');
            $injection_inventory->save();

            InjectionTransaction::create([
                'material_number' => $request->get('material_number'),
                'location' => 'RC91',
                'quantity' => $request->get('quantity'),
                'status' => 'INPUT STOCK',
                'operator_id' =>  Auth::user()->username,
                'created_by' => $id_user
            ]);

            $response = array(
                'status' => true
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function fetchInputStock(Request $request)
    {
        try {

            $stock = InjectionInventory::select('*','part_name as material_description')->join('injection_parts','injection_parts.gmc','injection_inventories.material_number')->where('location','RC91')->where('injection_parts.deleted_at',null)->where('injection_parts.remark','injection')->get();

            $response = array(
                'status' => true,
                'stock' => $stock
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexInjectionTag()
    {

        $material = DB::SELECT("SELECT *,gmc as material_number, part_name as material_description FROM `injection_parts` where remark = 'injection' and deleted_at is null");

        $title = 'Injection Tag';
        $title_jp = '???';
        return view('injection.tag',array(
            'title' => $title,
            'title_jp' => $title_jp,
            'material' => $material,
            'material2' => $material,
        ))->with('page', 'Injection Tag')->with('jpn', '???');
    }

    public function fetchInjectionTag(Request $request)
    {
        try {
            $tag = DB::SELECT('SELECT
                injection_tags.*,
                injection_parts.*,
                employee_syncs.*,
                concat( injection_tags.part_name, "<br>", injection_tags.part_type, " - ", injection_tags.color, "<br>", injection_tags.cavity ) AS partsall,
                injection_parts.part_name AS material_description,
                injection_tags.updated_at AS last_update,
                injection_tags.id AS id_tag 
            FROM
                injection_tags
                LEFT JOIN injection_parts ON injection_parts.gmc = injection_tags.material_number
                LEFT JOIN employee_syncs ON injection_tags.operator_id = employee_syncs.employee_id 
            WHERE
                injection_parts.deleted_at IS NULL');

            $response = array(
                'status' => true,
                'tag' => $tag
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function fetchInjectionMaterial(Request $request)
    {
        try {
            $material = InjectionTag::join('injection_parts','injection_parts.gmc','injection_tags.material_number')->where('material_number',$request->get('material_number'))->orderBy('injection_tags.id','desc')->first();

            $materialall = InjectionTag::orderBy('id','desc')->first();

            $response = array(
                'status' => true,
                'material' => $material,
                'materialall' => $materialall
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function fetchInjectionMaterialEdit(Request $request)
    {
        try {
            $material = InjectionTag::join('injection_parts','injection_parts.gmc','injection_tags.material_number')->where('material_number',$request->get('material_number'))->orderBy('injection_tags.id','desc')->first();

            $materialall = InjectionTag::orderBy('id','desc')->first();

            $response = array(
                'status' => true,
                'material' => $material,
                'materialall' => $materialall
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function inputInjectionTag(Request $request)
    {
        try {
            $id_user = Auth::id();
            InjectionTag::create([
                'material_number' => $request->get('material_number'),
                'no_kanban' => $request->get('no_kanban'),
                'concat_kanban' => $request->get('concat_kanban'),
                'tag' => $request->get('tag'),
                'mat_desc' => $request->get('mat_desc'),
                'created_by' => $id_user
            ]);
            
            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function editInjectionTag(Request $request)
    {
        try {
            $tag = InjectionTag::where('injection_tags.id',$request->get('id'))->first();

            $response = array(
                'status' => true,
                'tag' => $tag
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function updateInjectionTag(Request $request)
    {
        try {
            $id_user = Auth::id();
            $tag = InjectionTag::where('injection_tags.id',$request->get('id_tag'))->first();
            $tag->material_number = $request->get('material_number');
            $tag->mat_desc = $request->get('mat_desc');
            $tag->no_kanban = $request->get('no_kanban');
            $tag->tag = $request->get('tag');
            $tag->save();
            
            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function deleteInjectionTag($id)
    {
        try {
            InjectionTag::where('id',$id)->forceDelete();
            return redirect('index/injection/tag');
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function removeInjectionTag(Request $request)
    {
        try {
            $transaction = InjectionTag::where('tag',$request->get('tag'))->first();
            $transaction->operator_id = null;
            $transaction->part_name = null;
            $transaction->part_type = null;
            $transaction->color = null;
            $transaction->cavity = null;
            $transaction->location = null;
            $transaction->shot = null;
            $transaction->availability = null;
            $transaction->height_check = null;
            $transaction->push_pull_check = null;
            $transaction->torque_check = null;
            $transaction->remark = null;
            $transaction->save();

            $process = InjectionProcessLog::where('tag_product',$request->get('tag'))->where('material_number',$request->get('material_number'))->where('cavity',$request->get('cavity'))->where('remark',null)->first();
                $process->remark = 'Close';
                $process->save();

            $response = array(
                'status' => true
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexInjectionTraceability()
    {
        $title = 'Injection Traceability';
        $title_jp = '???';
        return view('injection.traceability',array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Injection Traceability')->with('jpn', '???');
    }

    public function fetchInjectionTraceability(Request $request)
    {
        try {
            if (is_numeric($request->get('tag'))) {
                $injection_process = DB::SELECT("SELECT
                    injection_process_logs.tag_product,
                    injection_tags.material_number,
                    injection_tags.mat_desc,
                    injection_tags.no_kanban,
                    injection_process_logs.start_time,
                    injection_process_logs.end_time,
                    injection_process_logs.mesin,
                    injection_process_logs.part_name,
                    injection_process_logs.part_type,
                    injection_process_logs.color,
                    injection_process_logs.cavity,
                    injection_process_logs.molding,
                    injection_process_logs.shot as qty,
                    injection_process_logs.dryer,
                    injection_process_logs.dryer_lot_number,
                    injection_process_logs.ng_name,
                    injection_process_logs.ng_count,
                    opmesin.employee_id,
                    opmesin.name
                FROM
                    injection_tags
                    LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                    LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
                WHERE
                    injection_process_logs.tag_product = '".$request->get('tag')."'  
                ORDER BY
                    injection_process_logs.created_at DESC
                    LIMIT 1
                    ");

                $molding = DB::SELECT("SELECT
                    injection_history_molding_logs.pic,
                    injection_history_molding_logs.mesin AS mesin,
                    injection_history_molding_logs.part,
                    injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                    injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                    injection_history_molding_logs.start_time,
                    injection_history_molding_logs.end_time,
                    injection_history_molding_logs.running_time,
                    injection_history_molding_logs.note,
                    COALESCE(injection_history_molding_logs.decision,'Tidak Ada') as decision
                FROM
                    injection_tags
                    LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                    LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                    LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                    LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                    left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
                WHERE
                    injection_process_logs.tag_product = '".$request->get('tag')."'  
                    AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                    AND injection_history_molding_logs.type = 'PASANG' 
                ORDER BY
                    injection_process_logs.created_at DESC,
                    injection_history_molding_logs.created_at DESC,
                    injection_molding_logs.created_at DESC 
                    LIMIT 1");

                $dryer = DB::SELECT("SELECT
                    injection_dryer_logs.material_number,
                    injection_dryer_logs.material_description,
                    injection_dryer_logs.dryer,
                    injection_dryer_logs.color,
                    injection_dryer_logs.qty,
                    injection_dryer_logs.lot_number,
                    injection_dryer_logs.created_at,
                    opresin.employee_id,
                    opresin.name
                FROM
                    injection_tags
                    LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                    LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                    AND injection_dryer_logs.dryer = injection_process_logs.dryer
                    LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
                WHERE
                    injection_process_logs.tag_product = '".$request->get('tag')."' 
                    AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
                ORDER BY
                    injection_process_logs.created_at DESC,
                    injection_dryer_logs.created_at DESC
                    LIMIT 1");

                $transaction = DB::SELECT("
                    SELECT
                        injection_transactions.material_number,
                        injection_tags.mat_desc,
                        injection_transactions.location,
                        injection_transactions.quantity,
                        injection_transactions.status,
                        opinjeksi.employee_id,
                        opinjeksi.name,
                        injection_transactions.created_at 
                    FROM
                        injection_tags
                        LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                        LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
                        LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
                    WHERE
                        injection_process_logs.tag_product = '".$request->get('tag')."' 
                        AND injection_transactions.created_at >= injection_process_logs.end_time 
                    ORDER BY
                        injection_process_logs.created_at DESC, 
                        injection_transactions.created_at ASC 
                        LIMIT 3");

                $response = array(
                    'status' => true,
                    'injection_process' => $injection_process,
                    'molding' => $molding,
                    'dryer' => $dryer,
                    'transaction' => $transaction,
                );
            }else{
                $tag = $request->get('tag');

                $datas = DB::SELECT('SELECT
                    *,
                    rc_kensa_initials.ng_name AS ng_name_injection,
                    rc_kensa_initials.ng_count AS ng_count_injection,
                    rc_kensas.ng_name AS ng_name_kensa,
                    rc_kensas.ng_count AS ng_count_kensa,
                    emp_injection.employee_id AS employee_id_injection,
                    emp_injection.NAME AS name_injection,
                    emp_resin.employee_id AS employee_id_resin,
                    emp_resin.NAME AS name_resin,
                    emp_kensa.employee_id AS employee_id_kensa,
                    emp_kensa.NAME AS name_kensa,   
                    part_injection.gmc AS material_number,
                    part_injection.part_name AS part_name,
                    part_resin.gmc AS material_number_resin,
                    part_resin.part_name AS mat_desc_resin,
                    rc_kensa_initials.part_type AS parts
                FROM
                    rc_kensa_initials
                    LEFT JOIN rc_kensas ON rc_kensas.serial_number = rc_kensa_initials.serial_number 
                    AND rc_kensas.material_number = rc_kensa_initials.material_number
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = rc_kensa_initials.operator_kensa
                    LEFT JOIN injection_parts part_injection ON part_injection.gmc = rc_kensa_initials.material_number
                    LEFT JOIN injection_parts part_resin ON part_resin.gmc = rc_kensa_initials.material_resin
                    LEFT JOIN employee_syncs emp_injection ON emp_injection.employee_id = operator_injection
                    LEFT JOIN employee_syncs emp_resin ON emp_resin.employee_id = operator_resin 
                    LEFT JOIN employee_syncs emp_kensa ON emp_kensa.employee_id = rc_kensas.operator_kensa
                WHERE
                    rc_kensa_initials.serial_number = "'.$tag.'" 
                    AND part_resin.deleted_at IS NULL
                    AND part_injection.deleted_at IS NULL');

                $data_kensa = DB::SELECT("SELECT
                  * 
                FROM
                  rc_kensas
                  LEFT JOIN employee_syncs ON employee_syncs.employee_id = operator_kensa
                  LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number 
                WHERE
                  serial_number = '".$tag."'
                  AND injection_parts.deleted_at IS NULL");
                
                $response = array(
                    'status' => true,
                    'datas' => $datas,
                    'data_kensa' => $data_kensa,
                );
            }
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexInjectionInventories()
    {
        $title = 'Injection Inventories';
        $title_jp = '???';
        return view('injection.inventory',array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Injection Inventories')->with('jpn', '???');
    }

    public function fetchInjectionInventories(Request $request)
    {
        try {
            $data = InjectionInventory::select('*','injection_inventories.updated_at as update_inventories')->join('injection_parts','injection_parts.gmc','injection_inventories.material_number')->where('injection_parts.deleted_at',null)->get();

            $response = array(
                'status' => true,
                'datas' => $data
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }
}
