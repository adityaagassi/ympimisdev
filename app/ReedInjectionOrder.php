<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedInjectionOrder extends Model{

	protected $fillable = [	
		'kanban',
		'material_number',
		'material_description',
		'quantity',
		'hako',
		'hako_delivered',
		'remark',
		'operator_molding_id',
		'setup_molding',
		'operator_injection_id',
		'start_injection',
		'finish_injection',
		'delivered_by',
		'delivered_at',
		'created_by'
	];

}
