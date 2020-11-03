<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JigSchedule extends Model
{
	use softDeletes;

	protected $fillable = [
		'jig_id', 'jig_index','schedule_date', 'kensa_time','kensa_pic','repair_pic', 'repair_time', 'kensa_status','repair_status','schedule_status', 'created_by'
	];
}
