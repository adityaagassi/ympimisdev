<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedWarehouseReceive extends Model
{
    protected $fillable = [	
		'receive_date',
		'material_number',
		'material_description',
		'quantity',
		'bag_quantity',
		'bag_delivered',
		'photo_date',
		'photo',
		'remark',
		'print_status',
		'operator_receive',
		'operator_storage',
		'operator_label',
		'created_by'
	];
}
