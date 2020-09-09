<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlanItem extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'machine_id', 'machine_name', 'description', 'category', 'location', 'area', 'remark', 'created_by'
	];
}
