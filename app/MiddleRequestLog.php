<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiddleRequestLog extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'material_tag', 'material_number', 'quantity', 'created_by'
	];
}
