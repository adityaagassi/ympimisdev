<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttachment extends Model{

	protected $fillable = [
		'employee_id', 'file_path', 'created_by', 
	];

}
