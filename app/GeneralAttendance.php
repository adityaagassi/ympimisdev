<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralAttendance extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'purpose_code', 'due_date', 'employee_id', 'attend_date', 'remark', 'created_by'
	];
}