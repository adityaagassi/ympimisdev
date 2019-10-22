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
        SELECT part, stock_akhir from stock_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m')='2019-10'
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

}
