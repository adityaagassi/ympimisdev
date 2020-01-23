<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopFlowProcess extends Model{

	use SoftDeletes;
	protected $fillable = [
		'order_no', 'sequence_process', 'machine_code', 'status', 'std_time', 'created_by'
	];

}
