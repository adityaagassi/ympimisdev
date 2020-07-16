<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'driver_id','name','destination_city','date_from','date_to','created_by','approved_by','received_by','remark','purpose'
	];
}