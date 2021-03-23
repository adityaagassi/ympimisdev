<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialRequirementPlan extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'due_date', 'material_number', 'quantity', 'remark', 'created_by'
	];
}
