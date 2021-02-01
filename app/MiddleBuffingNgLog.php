<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleBuffingNgLog extends Model{
    
	protected $fillable = [
		'tag', 'material_number', 'location', 'ng_name', 'quantity', 'employee_id', 'remark', 'started_at', 'operator_id', 'buffing_time', 'sync'
	];

}
