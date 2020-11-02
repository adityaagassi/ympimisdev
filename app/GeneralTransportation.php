<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralTransportation extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'employee_id','grade','zona','check_date','vehicle','attend_code','highway_amount','distance','highway_attachment','origin','destination','remark','created_by'
	];
}
