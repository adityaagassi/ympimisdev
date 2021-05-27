<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\ShipmentSchedule;
use App\KnockDownDetail;

class KDShipment extends Command{
    
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
    public function handle(){

        $knock_down_details = DB::select("SELECT kdd.id, kdd.kd_number, kdd.material_number, kdd.quantity, m.hpl, kd.`status` FROM knock_down_details AS kdd
            LEFT JOIN knock_downs AS kd ON kd.kd_number = kdd.kd_number
            LEFT JOIN materials AS m ON m.material_number = kdd.material_number 
            WHERE kdd.shipment_schedule_id IS NULL
            AND kd.`status` > 0
            ORDER BY kdd.kd_number ASC");

        foreach($knock_down_details as $knock_down_detail){

            if($knock_down_detail->hpl == 'MP'){

                $shipment_schedules = DB::select("SELECT ss.id, ss.quantity, ss.actual_quantity FROM shipment_schedules AS ss
                    WHERE ss.quantity < ss.actual_quantity
                    AND ss.material_number = '".$knock_down_detail->material_number."'
                    ORDER BY ss.st_date ASC");

                $found = 0;

                foreach($shipment_schedules as $shipment_schedule){
                    $diff = $shipment_schedule->quantity - $shipment_schedule->actual_quantity;

                    if($diff == $knock_down_detail->quantity){
                        try{
                            $update_shipment = ShipmentSchedule::where('id', '=', $shipment_schedule->id)
                            ->update([
                                'actual_quantity' => $shipment_schedule->actual_quantity+$knock_down_detail->quantity
                            ]);
                            $update_detail = KnockDownDetail::where('id', '=', $knock_down_detail->id)
                            ->update([
                                'shipment_schedule_id' => $shipment_schedule->id
                            ]);
                            $found = 1;
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

                if($found == 0){
                    $shipment_schedule = ShipmentSchedule::whereRaw('shipment_schedules.quantity > shipment_schedules.actual_quantity')
                    ->where('material_number', '=', $knock_down_detail->material_number)
                    ->orderBy('st_date', 'ASC')
                    ->get();

                    for ($i=0; $i < count($shipment_schedule); $i++) { 
                        $diff = $shipment_schedule->quantity - $shipment_schedule->actual_quantity;
                        $mod = $knock_down_detail->quantity % $diff;

                        if($diff >= $knock_down_detail->quantity){
                            if($mod == 0 || $knock_down_detail->quantity == 100){
                                try{
                                    $update_detail = KnockDownDetail::where('id', '=', $knock_down_detail->id)
                                    ->update([
                                        'shipment_schedule_id' => $shipment_schedule[$i]->id
                                    ]);

                                    $update_shipment = ShipmentSchedule::where('id', $shipment_schedule[$i]->id)->first();
                                    $update_shipment->actual_quantity = $update_shipment->actual_quantity + $knock_down_detail->quantity;
                                    $update_shipment->save();
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

            }else{
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
}
