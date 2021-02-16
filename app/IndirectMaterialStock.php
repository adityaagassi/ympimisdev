<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndirectMaterialStock extends Model{

	protected $fillable = [
		'in_date', 'exp_date', 'qr_code', 'material_number', 'cost_center_id', 'remark', 'quantity', 'bun', 'print_status', 'created_by'
	];

}
