<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedPackingOrder extends Model{

   protected $fillable = [	
		'kanban',
		'material_number',
		'material_description',
		'quantity',
		'hako',
		'hako_delivered',
		'remark',
		'operator_packing_id',
		'start_packing',
		'finish_packing',
		'created_by'
	];
}
