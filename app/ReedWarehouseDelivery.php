<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedWarehouseDelivery extends Model
{
     protected $fillable = [	
		'request_at',
		'kanban',
		'material_number',
		'material_description',
		'quantity',
		'bag_quantity',
		'bag_delivered',
		'remark',
		'operator_delivery',
		'delivery_at',
		'operator_receive',
		'receive_at',
		'created_by'
	];
}
