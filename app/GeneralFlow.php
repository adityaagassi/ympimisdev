<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralFlow extends Model
{
     protected $fillable = [
		'employee_id','flow_index','flow_name','remark','created_by'
	];
}
