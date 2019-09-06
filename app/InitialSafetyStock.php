<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InitialSafetyStock extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'material_number', 'valid_date', 'quantity', 'created_by'
	];
}
