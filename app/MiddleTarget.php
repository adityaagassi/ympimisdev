<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MiddleTarget extends Model{
	use SoftDeletes;

	protected $fillable = [
		'target_name', 'location', 'target', 'unit'
	];
}
