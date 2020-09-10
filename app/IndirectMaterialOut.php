<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class IndirectMaterialOut extends Model{

	protected $fillable = [
		'qr_code', 'material_number', 'cost_center_id', 'remark', 'quantity', 'bun', 'print_status', 'created_by'
	];
}
