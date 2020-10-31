<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralTransportationData extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id','check_time','attend_code','vehicle','vehicle_number','distance','highway_amount','remark','created_by'
	];
}
