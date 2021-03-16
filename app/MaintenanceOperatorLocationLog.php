<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceOperatorLocationLog extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'employee_id', 'employee_name', 'qr_code', 'machine_id', 'description', 'location', 'remark', 'logged_in_at', 'logged_out_at', 'created_by'
	];
}
