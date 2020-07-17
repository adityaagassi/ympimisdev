<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityItemNumber extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'utility_category', 'utility_type', 'utility_capacity', 'remark', 'item_number', 'created_by'
	];
}
