<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HealthIndicator extends Model
{
    protected $fillable = [
		'employee_id','name','type','type_id','source_name','value','unit','time_at','remark', 'created_by'
	];
}
