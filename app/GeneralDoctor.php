<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralDoctor extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'employee_id','doctor_name','diagnose','date_from','date_to','attachment_file','remark','created_by'
	];
}
