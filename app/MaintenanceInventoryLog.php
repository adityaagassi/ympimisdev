<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceInventoryLog extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'part_number', 'status', 'quantity', 'remark1', 'remark2', 'created_by'
	];
}
