<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaIncomingLog extends Model
{
     protected $fillable = [
		
		'incoming_check_code',
		'lot_number',
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
		'ng_ratio',
		'status_lot',
		'report_evidence',
		'send_email_status',
		'send_email_at',
		'created_by'
	];
}
