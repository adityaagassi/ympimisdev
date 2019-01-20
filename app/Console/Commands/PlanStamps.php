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


        if(date('D')=='Fri'){
            $h4 = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
        }
        elseif(date('D')=='Sat'){
            $h4 = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
        }
        else{
            $h4 = date('Y-m-d', strtotime(carbon::now()->addDays(2)));
        }

        if(date('D')=='Fri'){
            $h5 = date('Y-m-d', strtotime(carbon::now()->addDays(5)));
        }
        elseif(date('D')=='Sat'){
            $h5 = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
        }
        else{
            $h5 = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
        }


        $query = "select model, '" . $now . "' as due_date, sum(plan) as plan from
        (
        select materials.model, sum(plan) as plan from
        (
        select material_number, quantity as plan
        from production_schedules 
        where due_date >= '" . $first . "' and due_date <= '" . $h4 . "'

        union all

        select material_number, round(quantity*0.4) as plan
        from production_schedules 
        where due_date = '" . $h5 . "'

        union all

        select material_number, -(quantity) as plan
        from flo_details
        where date(created_at) >= '" . $first . "' and date(created_at) <= '" . $h4 . "'
        ) as plan
        left join materials on materials.material_number = plan.material_number
        group by materials.model

        union all

        select model, -(quantity) as plan
        from stamp_inventories
        ) as result
        group by model, due_date    
        having plan > 0";

        $planData = DB::select($query);

        foreach ($planData as $row) {
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