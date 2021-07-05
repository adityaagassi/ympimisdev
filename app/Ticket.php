<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'ticket_id', 'status', 'category', 'priority', 'priority_reason', 'case_title', 'case_description', 'case_before', 'case_after', 'document', 'due_date_from', 'due_date_to', 'estimated_due_date_from', 'estimated_due_date_to', 'actual_due_date_from', 'actual_due_date_to', 'pic_id', 'pic_name', 'difficulty', 'progress', 'project_name', 'remark', 'created_by', 'department'
	];
	
	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
