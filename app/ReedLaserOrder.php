<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedLaserOrder extends Model{

	protected $fillable = [	
		'kanban',
		'material_number',
		'material_description',
		'quantity',
		'hako',
		'hako_delivered',
		'remark',
		'operator_laser_id',
		'start_laser',
		'finish_laser',
		'operator_trimming_id',
		'start_trimming',
		'finish_trimming',
		'operator_annealing_id',
		'start_annealing',
		'finish_annealing',
		'created_by'
	];

}
