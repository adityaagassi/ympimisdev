<?php

use Illuminate\Database\Seeder;

class NavigationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'Dashboard',
    			'navigation_name' => 'Dashboard',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'A0',
                'navigation_name' => 'Administration Menu',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'A1',
    			'navigation_name' => 'Batch Setting',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'A2',
    			'navigation_name' => 'Code Generator',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'A3',
    			'navigation_name' => 'Navigation',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'A4',
    			'navigation_name' => 'Role',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'A5',
    			'navigation_name' => 'Status',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'A6',
    			'navigation_name' => 'User',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'M0',
                'navigation_name' => 'Master Menu',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M1',
    			'navigation_name' => 'Container',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M2',
    			'navigation_name' => 'Container Schedule',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M3',
    			'navigation_name' => 'Destination',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M4',
    			'navigation_name' => 'Material',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M5',
    			'navigation_name' => 'Material Volume',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M6',
    			'navigation_name' => 'Origin Group',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M7',
    			'navigation_name' => 'Production Schedule',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M8',
    			'navigation_name' => 'Shipment Condition',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M9',
    			'navigation_name' => 'Shipment Schedule',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M10',
    			'navigation_name' => 'Weekly Calendar',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M11',
    			'navigation_name' => 'Sales Budget',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'M12',
    			'navigation_name' => 'Sales Forecast',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'S0',
                'navigation_name' => 'Service Menu',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'S1',
    			'navigation_name' => 'FLO Band Instrument',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'S2',
    			'navigation_name' => 'FLO Educational Instrument',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'S3',
    			'navigation_name' => 'FLO Delivery',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'S4',
    			'navigation_name' => 'FLO Stuffing',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'S5',
    			'navigation_name' => 'FLO Shipment',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'S6',
    			'navigation_name' => 'FLO Lading',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'S7',
                'navigation_name' => 'Maedaoshi BI',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'S8',
                'navigation_name' => 'Maedaoshi EI',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'S9',
                'navigation_name' => 'FLO Deletion',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'S10',
                'navigation_name' => 'Counter Serial Number',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'R0',
                'navigation_name' => 'Report Menu',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'R1',
    			'navigation_name' => 'FLO Detail',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'R2',
    			'navigation_name' => 'Location Stock',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'R3',
    			'navigation_name' => 'Finished Goods',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'R4',
    			'navigation_name' => 'Chorei',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('navigations')->insert(
    		[
    			'navigation_code' => 'R5',
    			'navigation_name' => 'Display',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        DB::table('navigations')->insert(
            [
                'navigation_code' => 'R6',
                'navigation_name' => 'Transaction',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    }
}
