<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopMaterial extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'material_number', 'material_description', 'reamark', 'created_by'
	];

}
