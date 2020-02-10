<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopFlowProcess extends Model{

	use SoftDeletes;
	protected $fillable = [
		'order_no', 'sequence_process', 'machine_code', 'status', 'start_plan', 'finish_plan', 'std_time', 'operator', 'created_by'
	];

}
