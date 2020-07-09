<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityOrder extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'utility_id', 'no_pr', 'pr_data', 'created_by'
	];
}
