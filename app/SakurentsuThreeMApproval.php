<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuThreeMApproval extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'form_id', 'approver_name', 'approver_id', 'status', 'approve_at', 'created_by'
	];
}
