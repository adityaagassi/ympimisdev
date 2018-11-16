<?php

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('statuses')->insert(
    		[
    			'status_code' => '0',
    			'status_name' => 'Open',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('statuses')->insert(
    		[
    			'status_code' => '1',
    			'status_name' => 'Closed',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('statuses')->insert(
    		[
    			'status_code' => '2',
    			'status_name' => 'Delivered',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('statuses')->insert(
    		[
    			'status_code' => '3',
    			'status_name' => 'Loaded',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('statuses')->insert(
    		[
    			'status_code' => '4',
    			'status_name' => 'Departed',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        DB::table('statuses')->insert(
            [
                'status_code' => 'M',
                'status_name' => 'Maedaoshi',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        //
    }
}
