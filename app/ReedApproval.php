<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedApproval extends Model{
	
    protected $fillable = [	
		'date',
		'material_number',
		'material_description',
		'remark',
		'operator_id',
		'location',
		'process',
		'mesin',
		'resin',
		'parameter',
		'lot_resin',
		'created_by'
	];
}
