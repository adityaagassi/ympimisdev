<?php

use Illuminate\Database\Seeder;

class MaterialVolumesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('material_volumes')->insert(
    		[
    			'material_number' => 'WZ44910',
    			'category' => 'FG',
    			'type' => 'PLT',
    			'lot' => '100',
    			'length' => '1.3',
    			'width' => '0.85',
    			'height' => '2.13',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('material_volumes')->insert(
    		[
    			'material_number' => 'WZ44940',
    			'category' => 'FG',
    			'type' => 'PLT',
    			'lot' => '30',
    			'length' => '1.27',
    			'width' => '1.02',
    			'height' => '2.03',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('material_volumes')->insert(
    		[
    			'material_number' => 'ZS97790',
    			'category' => 'FG',
    			'type' => 'PLT',
    			'lot' => '240',
    			'length' => '1',
    			'width' => '1.15',
    			'height' => '1.97',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('material_volumes')->insert(
    		[
    			'material_number' => 'WZ44990',
    			'category' => 'FG',
    			'type' => 'PLT',
    			'lot' => '25',
    			'length' => '1.11',
    			'width' => '1.11',
    			'height' => '1.93',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('material_volumes')->insert(
    		[
    			'material_number' => 'WZ44910',
    			'category' => 'FG',
    			'type' => 'CTN',
    			'lot' => '10',
    			'length' => '0.79',
    			'width' => '0.63',
    			'height' => '0.4',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('material_volumes')->insert(
    		[
    			'material_number' => 'WZ44940',
    			'category' => 'FG',
    			'type' => 'CTN',
    			'lot' => '1',
    			'length' => '0.75',
    			'width' => '0.25',
    			'height' => '0.39',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('material_volumes')->insert(
    		[
    			'material_number' => 'ZS97790',
    			'category' => 'FG',
    			'type' => 'CTN',
    			'lot' => '10',
    			'length' => '0.56',
    			'width' => '0.34',
    			'height' => '0.46',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
    	DB::table('material_volumes')->insert(
    		[
    			'material_number' => 'WZ44990',
    			'category' => 'FG',
    			'type' => 'CTN',
    			'lot' => '1',
    			'length' => '0.82',
    			'width' => '0.25',
    			'height' => '0.36',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        //
    }
}
