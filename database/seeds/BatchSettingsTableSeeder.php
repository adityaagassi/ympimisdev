<?php

use Illuminate\Database\Seeder;

class BatchSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('batch_settings')->insert(
        [
          'batch_time' => '09:20',
          'upload' => '1',
          'download' => '0',
          'remark' => 'FLO',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('batch_settings')->insert(
        [
          'batch_time' => '16:50',
          'upload' => '1',
          'download' => '0',
          'remark' => 'FLO',
          'created_by' => '1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('batch_settings')->insert(
            [
              'batch_time' => '19:55',
              'upload' => '1',
              'download' => '0',
              'remark' => 'FLO',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
        );
        //
    }
  }
