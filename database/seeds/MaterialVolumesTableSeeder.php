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
                'lot_completion' => '1',
                'lot_transfer' => '1',
                'lot_pallet' => '1',
    			'lot_volume' => '1',
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
                'height' => '2.03',
    			'lot_completion' => '1',
                'lot_transfer' => '1',
                'lot_pallet' => '1',
                'lot_volume' => '1',
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
    			'lot_completion' => '1',
                'lot_transfer' => '1',
                'lot_pallet' => '1',
                'lot_volume' => '1',
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
    			'lot_completion' => '1',
                'lot_transfer' => '1',
                'lot_pallet' => '1',
                'lot_volume' => '1',
    			'length' => '1.11',
    			'width' => '1.11',
    			'height' => '1.93',
    			'created_by' => '1',
    			'created_at' => date('Y-m-d H:i:s'),
    			'updated_at' => date('Y-m-d H:i:s'),
    		]
    	);
        //
    }
}
