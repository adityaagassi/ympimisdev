<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\ShipmentSchedule;
use App\KnockDownDetail;

class KDShipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kd:shipment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $knock_down_details = DB::select("
            SELECT
            id,
            kd_number,
            material_number,
            quantity 
            FROM
            knock_down_details 
            WHERE
            shipment_schedule_id IS NULL 
            ORDER BY
            kd_number ASC");

        foreach($knock_down_details as $knock_down_detail){

            $shipment_schedule = ShipmentSchedule::whereRaw('shipment_schedules.quantity > shipment_schedules.actual_quantity')
            ->where('material_number', '=', $knock_down_detail->material_number)
            ->first();

            if($shipment_schedule != null){
                $diff = $shipment_schedule->quantity-$shipment_schedule->actual_quantity;

                if($diff >= $knock_down_detail->quantity){
                    try{
                        $update_detail = KnockDownDetail::where('id', '=', $knock_down_detail->id)
                        ->update([
                            'shipment_schedule_id' => $shipment_schedule->id
                        ]);

                        $shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $knock_down_detail->quantity;
                        $shipment_schedule->save();
                    }
                    catch(\Exception $e){
                        $error_log = new ErrorLog([
                            'error_message' => $e->getMessage(),
                            'created_by' => 1
                        ]);
                        $error_log->save();
                    }
                }
            }            
        }
    }
}
