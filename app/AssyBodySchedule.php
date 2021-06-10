<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssyBodySchedule extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'remark', 'material_number', 'due_date', 'quantity', 'created_by'
	];
}
