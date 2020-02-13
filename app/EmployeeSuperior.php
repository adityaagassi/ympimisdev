<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSuperior extends Model
{
    protected $fillable = [
		'employee_id', 'direct_superior', 'assignment_superior', 'created_by'
	];
}
