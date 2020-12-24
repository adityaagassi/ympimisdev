<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceJobPending extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'order_no', 'status', 'remark', 'description','time', 'created_by'
	];
}
