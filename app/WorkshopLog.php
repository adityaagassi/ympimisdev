<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class WorkshopLog extends Model{

	use SoftDeletes;
	protected $fillable = [
		'order_no', 'sequence_process', 'machine_code', 'operator_id', 'started_at' 
	];

}
