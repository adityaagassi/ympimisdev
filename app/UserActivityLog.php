<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivityLog extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'activity', 'created_by'
	];
}
