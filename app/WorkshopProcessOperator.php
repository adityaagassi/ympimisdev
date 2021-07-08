<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopProcessOperator extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'operator_id', 'process_id', 'created_by'
	];
}
