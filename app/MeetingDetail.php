<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingDetail extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'meeting_id', 'employee_id', 'name', 'department', 'created_by', 'employee_tag', 'status', 'remark', 'attend_time'
	];
}
