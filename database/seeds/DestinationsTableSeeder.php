<?php

use Illuminate\Database\Seeder;

class DestinationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {
    	DB::table('destinations')->insert(
        [
          'destination_code' => 'Y81804',
          'destination_name' => 'XIAOSHAN YAMAHA MUSICAL INSTRUMENT',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('destinations')->insert(

       [
        'destination_code' => 'Y81107',
        'destination_name' => 'SIAM MUSIC YAMAHA CO., LTD',
        'created_by' => '1',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]
    );
      DB::table('destinations')->insert(
        [
          'destination_code' => 'Y70801',
          'destination_name' => 'YAMAHA DE MEXICO S.A. DE C.V.',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('destinations')->insert(
        [
          'destination_code' => 'Y31507',
          'destination_name' => 'YAMAHA MUSIC MANUFACTURING JAPAN',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('destinations')->insert(
        [
          'destination_code' => 'Y1000Y',
          'destination_name' => 'YAMAHA CORPORATION',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
        //
    }
  }
