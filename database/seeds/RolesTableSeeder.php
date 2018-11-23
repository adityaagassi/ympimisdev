<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('roles')->insert(
    		[
    			'role_code' => 'S',
    			'role_name' => 'SUPERMAN',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        DB::table('roles')->insert(
            [
                'role_code' => 'G',
                'role_name' => 'Guest',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    	DB::table('roles')->insert(
    		[
    			'role_code' => 'OP-Assy-FL',
    			'role_name' => 'Operator Assembly Flute',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('roles')->insert(
    		[
    			'role_code' => 'OP-Assy-CL',
    			'role_name' => 'Operator Assembly Clarinet',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('roles')->insert(
    		[
    			'role_code' => 'OP-Assy-SX',
    			'role_name' => 'Operator Assembly Saxophone',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('roles')->insert(
    		[
    			'role_code' => 'OP-Assy-EI',
    			'role_name' => 'Operator Assembly Educational Instrument',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        DB::table('roles')->insert(
            [
                'role_code' => 'OP-WH-Exim',
                'role_name' => 'Operator Warehouse Exim',
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    }
}
