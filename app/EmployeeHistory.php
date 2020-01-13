<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeHistory extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'period','Emp_no','Full_name','grade_code', 'start_date', 'end_date', 'position_id', 'dept_id', 'pos_name_en', 'pos_code', 'parent_path', 'BOD', 'Division', 'Department', 'Section', 'Group', 'Sub-Group', 'status', 'employ_code', 'photo', 'gender', 'birthplace', 'birthdate', 'address', 'phone', 'identity_no', 'taxfilenumber', 'JP', 'BPJS', 'cost_center_name', 'cost_center_code', 'gradecategory_name', 'Penugasan', 'Union'
	];
}
