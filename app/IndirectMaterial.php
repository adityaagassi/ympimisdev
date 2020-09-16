<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndirectMaterial extends Model{
	protected $fillable = [
		'material_number', 'material_description', 'storage_location', 'expired', 'label', 'bun', 'valcl', 'created_by'
	];
}
