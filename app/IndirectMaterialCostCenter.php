<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndirectMaterialCostCenter extends Model{
    protected $fillable = [
		'cost_center', 'department', 'section', 'location', 'created_by'
	];
}
