<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class IndirectMaterialOut extends Model{

	protected $fillable = [
		'qr_code', 'material_number', 'cost_center_id', 'in_date', 'exp_date', 'created_by'
	];
}
