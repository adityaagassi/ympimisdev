<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\StampSchedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlanStamps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:stamps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating plan for stamp process';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $first = date('Y-m-01');
        $now = date('Y-m-d');
        $dayStock = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
        
        $target = DB::table('production_schedules')
        ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules.material_number')
        ->where('production_schedules.due_date', '=', $now)
        ->where('materials.category', '=', 'FG');

        $targetFL = $target->where('origin_group_code', '=', '041')->sum('production_schedules.quantity');
        $queryFLstock = "select sum(target_stock) as stock_fl from
        (
            select materials.model, if(stamp_inventories.quantity>sum(a.quantity), sum(a.quantity), stamp_inventories.quantity) as target_stock from
            (
                select material_number, quantity from production_schedules where due_date >= '" . $first . "' and due_date <= '" . $dayStock . "'

                union all

                select material_number, quantity*-1 as quantity from flo_details where date(created_at) >= '" . $first . "' and date(created_at) <= '" . $dayStock . "'
            ) as a
            left join materials on materials.material_number = a.material_number
            left join (select model, sum(quantity) as quantity from stamp_inventories group by model) as stamp_inventories on stamp_inventories.model = materials.model
            group by materials.model, stamp_inventories.quantity having target_stock > 0
        ) as b
        where model like 'YFL%'";
        
        $stockFL = DB::select($queryFLstock);

        if($targetFL != 0){
            $dayFL = floor($stockFL[0]->stock_fl/$targetFL);
            $addFL = ($stockFL[0]->stock_fl/$targetFL)-$dayFL;
        }
        else{
            $dayFL = 2;
            $addFL = 1;
        }

        if(date('D')=='Fri' || date('D')=='Thu' || date('D')=='Sat'){
            $hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+2)));
            $aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+3)));
        }
        elseif(date('D')=='Sun'){
            $hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+1)));
            $aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+2)));
        }
        else{
            $hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL)));
            $aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+1)));
        }
        
        // echo $targetFL.'-'.$stockFL.'-'.$dayFL.'-'.$addFL.'-'.$hFL.'-'.$aFL;
        // exit;

        $queryFL = "select model, '" . $now . "' as due_date, sum(plan) as plan from
        (
            select materials.model, sum(plan) as plan from
            (
                select material_number, quantity as plan
                from production_schedules 
                where due_date >= '" . $first . "' and due_date <= '" . $hFL . "'

                union all

                select material_number, round(quantity*".$addFL.",0) as plan
                from production_schedules 
                where due_date = '" . $aFL . "'

                union all

                select material_number, -(quantity) as plan
                from flo_details
                where date(created_at) >= '" . $first . "' and date(created_at) <= '" . $hFL . "'
            ) as plan
            left join materials on materials.material_number = plan.material_number
            group by materials.model

            union all

            select model, -(quantity) as plan
            from stamp_inventories where status is null
        ) as result
        group by model, due_date    
        having plan > 0 and model like 'YFL%' order by model asc";

        $planFL = DB::select($queryFL);

        foreach ($planFL as $row) {
            $model = $row->model;
            $due_date = $row->due_date;
            $quantity = $row->plan;

            $stamp_schedules = new StampSchedule([
                'model' => $model,
                'due_date' => $due_date,
                'quantity' => $quantity,
                'created_by' => 1
            ]);
            $stamp_schedules->save();
        }
    }
}