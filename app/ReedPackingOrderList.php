<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedPackingOrderList extends Model{

	protected $fillable = [	
		'order_id',
		'kanban',
		'material_number',
		'material_description',
		'picking_list',
		'picking_description',
		'process',
		'location',
		'quantity',
		'actual_quantity',
		'material_check',
		'check_description',
		'remark',
		'picked_by',
		'picked_at',
		'created_by'
	];
}
