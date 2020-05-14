<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityUse extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'utility_id', 'created_by'
	];
}
