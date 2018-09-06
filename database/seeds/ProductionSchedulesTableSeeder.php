<?php

use Illuminate\Database\Seeder;

class ProductionSchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('production_schedules')->insert(
    		[
    			'material_number' => 'WZ44910',
    			'destination_code' => 'Y81107',
    			'due_date' => '2018-09-03',
    			'quantity' => '20',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('production_schedules')->insert(
    		[
    			'material_number' => 'WZ44910',
    			'destination_code' => 'Y1000Y',
    			'due_date' => '2018-09-04',
    			'quantity' => '20',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('production_schedules')->insert(
    		[
    			'material_number' => 'WZ44940',
    			'destination_code' => 'Y81107',
    			'due_date' => '2018-09-05',
    			'quantity' => '20',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('production_schedules')->insert(
    		[
    			'material_number' => 'WZ44940',
    			'destination_code' => 'Y70801',
    			'due_date' => '2018-09-06',
    			'quantity' => '20',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        //
    }
}
