<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class IndirectMaterialSchedule extends Model{
	use SoftDeletes;

	protected $fillable = [
		'schedule_date', 'schedule_shift', 'category', 'solution_id', 'material_number', 'cost_center_id', 'storage_location', 'quantity', 'bun', 'actual_quantity', 'picked_by', 'picked_time', 'changed_by', 'changed_time', 'note', 'created_by'
	];

}
