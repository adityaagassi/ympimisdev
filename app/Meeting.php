<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'organizer_id', 'subject', 'description', 'location', 'start_time', 'end_time', 'status', 'remark', 'created_by'
	];
}
