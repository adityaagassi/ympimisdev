<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceOperatorLocation extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'employee_id', 'employee_name', 'qr_code', 'machine_id', 'description', 'location', 'remark', 'created_by'
	];
}
