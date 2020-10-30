<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MouthpieceChecksheet extends Model
{
	use SoftDeletes;

	protected $fillable = [	
		'kd_number', 'material_number', 'quantity', 'actual_quantity', 'remark', 'shipment_schedule_id', 'created_by', 'start_packing', 'end_packing', 'print_status', 'st_date', 'destination_shortname', 'material_description', 'qa_check', 'packing_date'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}