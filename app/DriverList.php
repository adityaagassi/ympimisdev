<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverList extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'driver_id','name','created_by', 'reamark'
	];
}
