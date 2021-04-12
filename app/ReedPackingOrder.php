<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedPackingOrder extends Model{

   protected $fillable = [	
		'order_id',
		'due_date',
		'material_number',
		'material_description',
		'quantity',
		'hako',
		'hako_delivered',
		'print',
		'remark',
		'operator_packing_id',
		'start_packing',
		'finish_packing',
		'created_by'
	];
}
