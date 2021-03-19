<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaCode extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'id', 'area_code', 'area', 'remark', 'created_by'
	];
}
