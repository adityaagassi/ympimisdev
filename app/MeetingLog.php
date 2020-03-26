<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingLog extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'meeting_id', 'employee_id', 'created_by', 'employee_tag', 'status', 'remark', 'attend_time', 'organizer_id', 'subject', 'description', 'location', 'start_time', 'end_time', 'status'
	];
}
