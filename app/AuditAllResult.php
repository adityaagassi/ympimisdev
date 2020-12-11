<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditAllResult extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'tanggal','kategori','auditor_id','auditor_name','lokasi','auditee','auditee_name','point_judul','foto','note','status_ditangani','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
