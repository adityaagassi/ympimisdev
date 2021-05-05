<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanteenPurchaseRequisition extends Model
{
    protected $fillable = [
		'no_pr','emp_id','emp_name','department','section','submission_date','receive_date','file','file_pdf','note','posisi','status','no_budget','manager','manager_name','gm','approvalm','dateapprovalm','approvalgm','dateapprovalgm','alasan','datereject','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
