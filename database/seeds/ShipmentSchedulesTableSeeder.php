<?php

use Illuminate\Database\Seeder;

class ShipmentSchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('shipment_schedules')->insert(
    		[
    			'st_month' => '2018-09-01',
    			'sales_order' => '170205',
                'shipment_condition_code' => 'C1',
    			'destination_code' => 'Y1000Y',
    			'material_number' => 'WZ44910',
    			'hpl' => 'CLFG',
                'st_date' => '2018-09-05',
    			'bl_date' => '2018-09-10',
    			'quantity' => '100',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('shipment_schedules')->insert(
    		[
    			'st_month' => '2018-09-01',
    			'sales_order' => '170205',
                'shipment_condition_code' => 'C1',
    			'destination_code' => 'Y1000Y',
    			'material_number' => 'WZ44940',
    			'hpl' => 'ASFG',
                'st_date' => '2018-09-05',
    			'bl_date' => '2018-09-10',
    			'quantity' => '30',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('shipment_schedules')->insert(
    		[
    			'st_month' => '2018-09-01',
    			'sales_order' => '169988',
                'shipment_condition_code' => 'C2',
    			'destination_code' => 'Y70801',
    			'material_number' => 'WZ44990',
    			'hpl' => 'TSFG',
                'st_date' => '2018-09-15',
    			'bl_date' => '2018-09-18',
    			'quantity' => '422',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('shipment_schedules')->insert(
    		[
    			'st_month' => '2018-09-01',
    			'sales_order' => '170233',
                'shipment_condition_code' => 'C1',
    			'destination_code' => 'Y81107',
    			'material_number' => 'ZS97790',
    			'hpl' => 'FLFG',
                'st_date' => '2018-09-20',
    			'bl_date' => '2018-09-25',
    			'quantity' => '500',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        //
    }
}
