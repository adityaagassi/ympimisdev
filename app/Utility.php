<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Utility extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'utility_code', 'utility_name', 'type', 'location', 'group', 'capacity', 'remark', 'status', 'exp_date', 'created_by'
	];
}
