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
              'remark' => 'FLO',
              'upload' => '6',
              'download' => '0',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
        );
        //
    }
}
