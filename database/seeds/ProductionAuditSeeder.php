<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductionAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 1;$i<=4; $i++){
        	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '47',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.40.40.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '48',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.42.08.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '49',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '50',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '51',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '52',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);
	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '53',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);
	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '54',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);
	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '55',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);
	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '56',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

        	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '57',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.40.40.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '58',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.42.08.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '59',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '60',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '61',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);

	    	DB::table('production_audits')->insert(
	    		[
	    			'activity_list_id' => '13',
	    			'point_check_audit_id' => '62',
	    			'date' => '2019-11-0'.$i,
	    			'foto_kondisi_aktual' => '2019-11-01 15.43.00.jpg',
	    			'kondisi' => 'Good',
	    			'pic' => 'B98070144',
	    			'auditor' => 'F02030888',
	    			'created_by' => '1930',
	    			'created_at' => date('Y-m-d H:i:s'),
	    			'updated_at' => date('Y-m-d H:i:s'),
	    		]
	    	);
        }
    }
}
