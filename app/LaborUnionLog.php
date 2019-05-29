<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaborUnionLog extends Model
{
	use SoftDeletes;
	
	protected $fillable = [
		'employee_id', 'union', 'valid_from', 'valid_to', 'created_by', 'created_by'
	];
}
