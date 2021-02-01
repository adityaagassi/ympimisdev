<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleBuffingLog extends Model {
	
	protected $fillable = [
		'tag', 'material_number', 'location', 'quantity', 'employee_id', 'remark', 'started_at', 'operator_id', 'buffing_time'
	];

}
