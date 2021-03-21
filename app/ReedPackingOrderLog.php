<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedPackingOrderLog extends Model{

	protected $fillable = [	
		'order_id',
		'kanban',
		'material_number',
		'material_description',
		'picking_list',
		'picking_description',
		'location',
		'quantity',
		'remark',
		'picked_by',
		'picked_at',
		'created_by'
	];
}
