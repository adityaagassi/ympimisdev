<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CparDepartment extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'kategori','judul','tanggal','section_from','section_to','target','jumlah','waktu','aksi','posisi','status','pelapor','grade','chief','foreman','manager','approvalcf','datecf','approvalm','datem','alasan','datereject','tanggal_car','pic_car','deskripsi_car','penanganan_car','chief_car','foreman_car','manager_car','approvalcf_car','datecf_car','approvalm_car','datem_car','alasan_car','datereject_car','reject_all','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
