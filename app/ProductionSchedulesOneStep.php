<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionSchedulesOneStep extends Model{

	protected $fillable = [
		'due_date', 'material_number', 'quantity', 'actual_quantity', 'created_by'
	];

}
