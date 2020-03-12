<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CparDepartment extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'kategori','judul','tanggal','section_from','section_to','target','jumlah','waktu','aksi','approvalcf','approvalm','pelapor','chief','foreman','manager','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
