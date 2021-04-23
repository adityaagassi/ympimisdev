<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafetyRiding extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'period','department','employee_id','employee_name','safety_riding','location','created_by'
	];
}
