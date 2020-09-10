<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class IndirectMaterialPickingLog extends Model{

	protected $fillable = [
		'schedule_id', 'qr_code', 'material_number', 'cost_center_id', 'remark', 'quantity', 'bun', 'created_by'
	];
	
}