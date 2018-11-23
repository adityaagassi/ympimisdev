<?php

use Illuminate\Database\Seeder;

class OriginGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('origin_groups')->insert(
    		[
    			'origin_group_code' => '041',
    			'origin_group_name' => 'Flute',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('origin_groups')->insert(

    		[
    			'origin_group_code' => '042',
    			'origin_group_name' => 'Clarinet',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('origin_groups')->insert(
    		[
    			'origin_group_code' => '043',
    			'origin_group_name' => 'Saxophone',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('origin_groups')->insert(
    		[
    			'origin_group_code' => '027',
    			'origin_group_name' => 'Venova',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('origin_groups')->insert(
    		[
    			'origin_group_code' => '072',
    			'origin_group_name' => 'Recorder',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('origin_groups')->insert(
    		[
    			'origin_group_code' => '073',
    			'origin_group_name' => 'Pianica',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	// DB::table('origin_groups')->insert(
    	// 	[
    	// 		'origin_group_code' => 'A02',
    	// 		'origin_group_name' => 'CASE',
    	// 		'created_by' => '1',
    	// 		'created_at' => date('Y-m-d H:i:s'),
    	// 		'updated_at' => date('Y-m-d H:i:s'),
    	// 	]
    	// );
    	// DB::table('origin_groups')->insert(
    	// 	[
    	// 		'origin_group_code' => 'A03',
    	// 		'origin_group_name' => 'IN-DIRECT',
    	// 		'created_by' => '1',
    	// 		'created_at' => date('Y-m-d H:i:s'),
    	// 		'updated_at' => date('Y-m-d H:i:s'),
    	// 	]
    	// );
        //
    }
}
