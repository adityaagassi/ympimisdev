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
          'container_id' => '20180901',
          'container_code' => 'F49',
          'destination_code' => 'Y1000Y',
          'shipment_date' => '2018-11-25',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('container_schedules')->insert(
        [
          'container_id' => '20180902',
          'container_code' => 'F49',
          'destination_code' => 'Y1000YJ',
          'shipment_date' => '2018-11-25',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('container_schedules')->insert(
        [
          'container_id' => '20180903',
          'container_code' => 'F49',
          'destination_code' => 'Y31507',
          'shipment_date' => '2018-11-25',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('container_schedules')->insert(
        [
          'container_id' => '20180904',
          'container_code' => 'F49',
          'destination_code' => 'Y70801',
          'shipment_date' => '2018-11-24',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('container_schedules')->insert(
        [
          'container_id' => '20180905',
          'container_code' => 'F49',
          'destination_code' => 'Y31507',
          'shipment_date' => '2018-11-23',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('container_schedules')->insert(
        [
          'container_id' => '20180906',
          'container_code' => 'F49',
          'destination_code' => 'Y81107',
          'shipment_date' => '2018-11-23',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
        //
    }
}
