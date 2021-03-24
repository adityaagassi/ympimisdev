<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralAttendanceLog extends Model
{
    protected $fillable = [
		'purpose_code', 'due_date', 'employee_id', 'attend_date', 'remark', 'remark', 'remark2', 'created_by'
	];
}
