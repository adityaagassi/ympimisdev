<?php

use Illuminate\Database\Seeder;

class ContainersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('containers')->insert(
        [
          'container_code' => 'F20',
          'container_name' => 'Container 20 feet',
          'capacity' => '32.85828',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('containers')->insert(

       [
        'container_code' => 'F40',
        'container_name' => 'Container 40 feet',
        'capacity' => '66.8304',
        'created_by' => '1',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]
    );
      DB::table('containers')->insert(
        [
          'container_code' => 'F49',
          'container_name' => 'Container 40 feet high cube',
          'capacity' => '73.008',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('containers')->insert(
        [
          'container_code' => 'TR',
          'container_name' => 'Truck',
          'capacity' => '0',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
    }
  }
