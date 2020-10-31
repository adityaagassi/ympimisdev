<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralTransportation extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'employee_id','grade','zona','check_date','check_time','attend_code','vehicle','vehicle_number','highway_amount','distance','highway_attachment','remark','created_by'
	];
}
