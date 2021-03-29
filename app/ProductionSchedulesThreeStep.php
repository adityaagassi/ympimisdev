<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionSchedulesThreeStep extends Model{
	
	protected $fillable = [
		'st_month', 'sales_order', 'shipment_condition_code', 'destination_code', 'material_number', 'hpl', 'bl_date', 'st_date', 'quantity' , 'actual_quantity', 'created_by'
	];
	
}
