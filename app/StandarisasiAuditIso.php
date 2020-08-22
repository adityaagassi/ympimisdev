<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandarisasiAuditIso extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'tanggal','kategori','auditor_id','auditor_name','lokasi','klausul','point_judul','point_question','status','foto','note','status_ditangani','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
