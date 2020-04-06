<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingGroup extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'subject', 'description', 'employee_id'
	];
}
