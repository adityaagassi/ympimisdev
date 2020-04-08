<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnAdditional extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'material_number', 'description', 'issue_location', 'receive_location', 'created_by'
	];
}
