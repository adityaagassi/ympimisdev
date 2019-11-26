<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KaizenForm extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'employee_id', 'employee_name', 'propose_date', 'section', 'leader', 'title', 'condition', 'improvement', 'purpose', 'area', 'status'
	];

}
