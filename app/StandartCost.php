<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandartCost extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'cost_name', 'cost', 'unit', 'frequency','category', 'created_by'
	];
}
