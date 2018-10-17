<?php

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	//1
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'S',
    			'department_name' => 'Superman',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//2
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'HR',
    			'department_name' => 'Human Resources',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//3
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'GA',
    			'department_name' => 'General Affairs',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//4
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'FIN',
    			'department_name' => 'Finance',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//5
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'PE',
    			'department_name' => 'Production Engineering',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//6
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'PM',
    			'department_name' => 'Plant Maintenance',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//7
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'MIS',
    			'department_name' => 'Management Information System',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//8
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'PC',
    			'department_name' => 'Production Control',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//9
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'PCH',
    			'department_name' => 'Purchasing',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//10
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'LOG',
    			'department_name' => 'Logistics',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//11
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'QA',
    			'department_name' => 'Quality Assurance',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//12
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'PP',
    			'department_name' => 'Parts Process',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//13
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'EI',
    			'department_name' => 'Educational Instrument',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//14
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'AP',
    			'department_name' => 'Assembly Process',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//15
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'WST',
    			'department_name' => 'Welding-Surface Treatment Process',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	//16
    	DB::table('departments')->insert(
    		[
    			'department_code' => 'JPN',
    			'department_name' => 'Expratriat',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    }
}
