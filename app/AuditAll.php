<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditAll extends Model
{
    
    use SoftDeletes;

    protected $fillable = [
		'auditor','auditor_name','posisi','auditor_date','auditor_jenis','auditor_lokasi','auditor_permasalahan','auditor_penyebab','auditor_bukti','auditee','auditee_name','auditee_due_date','auditee_perbaikan','auditee_pencegahan','auditee_biaya','auditee_date','created_by'
	];
	
	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
