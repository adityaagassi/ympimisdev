<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndirectMaterialPick extends Model{

    protected $fillable = [
		'qr_code', 'schedule_id', 'material_number', 'cost_center_id', 'remark', 'in_date', 'exp_date', 'created_by'
	];

}
