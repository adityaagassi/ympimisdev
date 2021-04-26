<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecordStockMiddle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record:stock_middle';

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

        $date = date('Y-m-d');
        $first = date('Y-m-01', strtotime($date));
        $now = date('Y-m-d', strtotime($date));
        $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        $end = date('Y-m-t', strtotime($date));

        // ( histories.transfer_movement_type = '9I4', -( histories.lot ), 0 ))) AS picking,
        // ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, -(histories.lot)),0))) AS picking,

        // ( histories.transfer_movement_type = '9I4', -( histories.lot ), 0 )))) AS plan,
        // ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, -(histories.lot)),0)))) AS plan,

        // ( histories.transfer_movement_type = '9I4', ( histories.lot ), 0 )) AS minus,
        // ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, histories.lot),0)) AS minus,


        try{
            $stockroom_keys = db::select("INSERT INTO middle_stocks (
                issue_storage_location,
                hpl,
                model,
                `key`,
                surface,
                plan,
                picking,
                plus,
                minus,
                stock,
                plan_ori,
                diff,
                diff2,
                ava,
                ultra_safe,
                safe,
                unsafe,
                zero,
                created_at,
                updated_at
                )
                SELECT
                materials.issue_storage_location,
                materials.hpl,
                materials.model,
                materials.`key`,
                materials.surface,
                sum( plan ) AS plan,
                sum( picking ) AS picking,
                sum( plus ) AS plus,
                sum( minus ) AS minus,
                sum( stock ) AS stock,
                sum( plan_ori ) AS plan_ori,
                (
                sum( plan )- sum( picking )) AS diff,
                sum( stock ) - (
                sum( plan )- sum( picking )) AS diff2,
                round( sum( stock ) / sum( plan ), 1 ) AS ava,
                IF
                ( round( sum( stock ) / sum( plan ), 1 )> 2, 1, 0 ) AS ultra_safe,
                IF
                (
                round( sum( stock ) / sum( plan ), 1 )>= 1 
                AND round( sum( stock ) / sum( plan ), 1 )<= 2,
                1,
                0 
                ) AS safe,
                IF
                (
                round( sum( stock ) / sum( plan ), 1 )< 1 
                AND round( sum( stock ) / sum( plan ), 1 )> 0,
                1,
                0 
                ) AS unsafe,
                IF
                ( sum( stock )<= 0, 1, 0 ) AS zero ,
                '".date("Y-m-d H:i:s")."' as created_at,
                '".date("Y-m-d H:i:s")."' as updated_at
                FROM
                (
                SELECT
                material_number,
                sum( plan ) AS plan,
                sum( picking ) AS picking,
                sum( plus ) AS plus,
                sum( minus ) AS minus,
                sum( stock ) AS stock,
                sum( plan_ori ) AS plan_ori 
                FROM
                (
                SELECT
                material_number,
                plan,
                picking,
                plus,
                minus,
                stock,
                plan_ori 
                FROM
                (
                SELECT
                materials.material_number,
                0 AS plan,
                sum(
                IF
                (
                histories.transfer_movement_type = '9I3',
                histories.lot,
                IF
                ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, -(histories.lot)),0))) AS picking,
                0 AS plus,
                0 AS minus,
                0 AS stock,
                0 AS plan_ori 
                FROM
                (
                SELECT
                materials.id,
                materials.material_number 
                FROM
                kitto.materials 
                WHERE
                materials.location IN ( 'SX51', 'CL51' ) 
                AND category = 'key'
                ) AS materials
                LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id 
                WHERE
                date( histories.created_at ) = '".$now."' 
                AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' ) 
                GROUP BY
                materials.material_number 
                ) AS pick UNION ALL
                SELECT
                inventories.material_number,
                0 AS plan,
                0 AS picking,
                0 AS plus,
                0 AS minus,
                sum( inventories.lot ) AS stock,
                0 AS plan_ori 
                FROM
                kitto.inventories
                LEFT JOIN kitto.materials ON materials.material_number = inventories.material_number 
                WHERE
                materials.location IN ( 'SX51', 'CL51' ) 
                AND materials.category = 'key'
                GROUP BY
                inventories.material_number UNION ALL
                SELECT
                material_number,
                sum( plan ) AS plan,
                0 AS picking,
                0 AS plus,
                0 AS minus,
                0 AS stock,
                sum( plan_ori ) AS plan_ori 
                FROM
                (
                SELECT
                materials.material_number,
                -(
                sum(
                IF
                (
                histories.transfer_movement_type = '9I3',
                histories.lot,
                IF
                ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, -(histories.lot)),0)))) AS plan,
                0 AS plan_ori 
                FROM
                (
                SELECT
                materials.id,
                materials.material_number 
                FROM
                kitto.materials 
                WHERE
                materials.location IN ( 'SX51', 'CL51' ) 
                AND category = 'key' 
                ) AS materials
                LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id 
                WHERE
                date( histories.created_at ) >= '".$first."' 
                AND date( histories.created_at ) <= '".$yesterday."' 
                AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' ) 
                GROUP BY
                materials.material_number UNION ALL
                SELECT
                assy_picking_schedules.material_number,
                sum( quantity ) AS plan,
                sum( quantity ) AS plan_ori 
                FROM
                assy_picking_schedules
                LEFT JOIN materials ON materials.material_number = assy_picking_schedules.material_number 
                WHERE
                due_date >= '".$first."' 
                AND due_date <= '".$now."' 
                AND assy_picking_schedules.remark IN ( 'SX51', 'CL51' ) 
                GROUP BY
                assy_picking_schedules.material_number 
                ) AS plan 
                GROUP BY
                material_number UNION ALL
                SELECT
                materials.material_number,
                0 AS plan,
                0 AS picking,
                sum(
                IF
                ( histories.transfer_movement_type = '9I3', histories.lot, 0 )) AS plus,
                sum(
                IF
                ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, histories.lot),0)) AS minus,
                0 AS stock,
                0 AS plan_ori 
                FROM
                (
                SELECT
                materials.id,
                materials.material_number 
                FROM
                kitto.materials 
                WHERE
                materials.location IN ( 'SX51', 'CL51' ) 
                AND category = 'key'
                ) AS materials
                LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id 
                WHERE
                date( histories.created_at ) >= '".$first."' 
                AND date( histories.created_at ) <= '".$now."' 
                AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' ) 
                GROUP BY
                materials.material_number 
                ) AS final 
                GROUP BY
                material_number 
                HAVING
                plan_ori > 0 
                ) AS final2
                JOIN materials ON final2.material_number = materials.material_number 
                GROUP BY
                materials.issue_storage_location,
                materials.hpl,
                materials.model,
                materials.`key`,
                materials.surface,
                materials.issue_storage_location 
                HAVING 
                diff > 0
                ORDER BY
                diff DESC");
}
catch (Exception $e) {
    $response = array(
        'status' => false,
        'message' => $e->getMessage(),
    );
    return Response::json($response);
}   

}
}
