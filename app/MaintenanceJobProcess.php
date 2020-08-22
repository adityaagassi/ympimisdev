<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceJobProcess extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'order_no', 'operator_id', 'start_plan', 'finish_plan', 'start_actual', 'finish_actual', 'remark', 'created_by'
	];
}
