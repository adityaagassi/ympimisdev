<?php

use Illuminate\Database\Seeder;

class ContainerSchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('container_schedules')->insert(
        [
          'container_code' => 'F49',
          'destination_code' => 'Y1000Y',
          'quantity' => '2',
          'shipment_date' => '2018-08-31',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
    	DB::table('container_schedules')->insert(
        [
          'container_code' => 'F49',
          'destination_code' => 'Y31507',
          'quantity' => '1',
          'shipment_date' => '2018-08-31',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
    	DB::table('container_schedules')->insert(
        [
          'container_code' => 'F49',
          'destination_code' => 'Y81804',
          'quantity' => '3',
          'shipment_date' => '2018-08-31',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
    	DB::table('container_schedules')->insert(
        [
          'container_code' => 'F49',
          'destination_code' => 'Y1000Y',
          'quantity' => '1',
          'shipment_date' => '2018-08-24',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
    	DB::table('container_schedules')->insert(
        [
          'container_code' => 'F49',
          'destination_code' => 'Y31507',
          'quantity' => '1',
          'shipment_date' => '2018-08-24',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
    	DB::table('container_schedules')->insert(
        [
          'container_code' => 'F49',
          'destination_code' => 'Y81804',
          'quantity' => '2',
          'shipment_date' => '2018-08-24',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
        //
    }
}
