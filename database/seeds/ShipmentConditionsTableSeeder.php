<?php

use Illuminate\Database\Seeder;

class ShipmentConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('shipment_conditions')->insert(
            [
              'shipment_condition_code' => 'C1',
              'shipment_condition_name' => 'SEA',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
        );
        DB::table('shipment_conditions')->insert(
            [
              'shipment_condition_code' => 'C2',
              'shipment_condition_name' => 'AIR',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
        );
        //
    }
}
