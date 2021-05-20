<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialPlanDelivery extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'due_date', 'material_number', 'quantity', 'remark', 'created_by'
	];
}
