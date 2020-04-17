<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plc extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'location', 'station', 'address', 'arr', 'remark', 'created_by', 'upper_limit', 'lower_limit'
	];
}
