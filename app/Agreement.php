<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'department', 'vendor', 'description', 'valid_from', 'valid_to', 'status', 'remark', 'created_by'
	];
}
