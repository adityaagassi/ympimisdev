<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopMaterial extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'item_number', 'item_description', 'file_name', 'remark', 'created_by'
	];

}
