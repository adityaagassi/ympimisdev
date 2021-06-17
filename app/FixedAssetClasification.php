<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetClasification extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'category_code', 'clasification_name', 'category', 'life_time', 'remark', 'created_by'
	];
}
