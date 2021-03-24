<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $table = "mutasi_depts";

    protected $fillable = [
		'status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi','ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'position_code', 'tanggal', 'tanggal_maksimal', 'alasan', 'created_by', 'remark', 

		'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
		'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
		'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
		'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan', 
		'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan', 
		'manager_hrga', 'nama_manager', 'date_manager_hrga',
		
		'app_ca','app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m'
	];

    public function mutasi()
	{
		return $this->belongsTo('App\Mutasi', 'mutasi_nik', 'mutasi_nik')->withTrashed();
	}
}
