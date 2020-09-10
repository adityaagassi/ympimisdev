<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndirectMaterialLog extends Model{
	use SoftDeletes;

	protected $fillable = [
		'qr_code', 'material_number', 'cost_center_id', 'remark', 'quantity', 'bun', 'print_status', 'created_by'
	];
	
}
