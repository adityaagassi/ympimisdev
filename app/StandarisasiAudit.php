<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandarisasiAudit extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'audit_no','auditor','auditor_name','posisi','status','auditor_date','auditor_jenis','auditor_lokasi','auditor_kategori','auditor_persyaratan','auditor_permasalahan','auditor_penyebab','auditor_bukti','auditee','auditee_name','auditee_due_date','auditee_perbaikan','auditee_pencegahan','auditee_biaya','auditee_date','alasan','auditor_catatan','auditor_manfaat','created_by'
	];
	
	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

}