<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id', 'status', 'valid_from', 'valid_to', 'created_by'
	];
}
