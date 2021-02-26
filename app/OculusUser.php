<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OculusUser extends Model
{
    protected $fillable = [
		'employee_id','name', 'created_by'
	];
}
