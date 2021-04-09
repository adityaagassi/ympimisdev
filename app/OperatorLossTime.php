<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatorLossTime extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'employee_id', 'employee_name', 'cost_center', 'position', 'division', 'department', 'section', 'section', 'group', 'sub_group', 'reason'
	];
}
