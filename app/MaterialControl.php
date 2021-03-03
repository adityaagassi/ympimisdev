<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialControl extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'material_number', 'material_description', 'vendor_code', 'vendor_name', 'catgegory', 'pic', 'remark', 'created_by'
	];
}
