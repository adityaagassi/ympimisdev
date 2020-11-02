<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralTransportationData extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id','attend_code','distance','vehicle','highway_amount','origin','destination','remark','created_by'
	];
}
