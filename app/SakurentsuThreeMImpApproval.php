<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuThreeMImpApproval extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'form_id', 'implement_id', 'approver_name', 'approver_id', 'status', 'remark', 'approve_at', 'created_by'
	];
}
