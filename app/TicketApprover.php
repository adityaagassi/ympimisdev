<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketApprover extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'ticket_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'approver_email'
	];
}
