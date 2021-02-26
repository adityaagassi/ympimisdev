<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OculusResult extends Model
{
    protected $fillable = [
		'employee_id','name','oculus_answer','oculus_sub_answer','oculus_result', 'created_by'
	];
}
