<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiddleRequestHelper extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'material_tag', 'material_number', 'created_by', 'created_at', 'updated_at', 'deleted_at'
	];
}
