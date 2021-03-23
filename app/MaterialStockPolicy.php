<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialStockPolicy extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'period', 'material_number', 'material_description', 'policy', 'created_by'
	];
}
