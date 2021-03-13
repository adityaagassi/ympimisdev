<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaIncomingLog extends Model
{
     protected $fillable = [
		
		'incoming_check_code',
		'location',
		'inspector_id',
		'material_number', 
		'material_description',
		'vendor',
		'qty_rec',
		'qty_check',
		'invoice',
		'inspection_level',
		'repair',
		'scrap',
		'return',
		'total_ok',
		'total_ng',
		'status_lot',
		'created_by'
	];
}
