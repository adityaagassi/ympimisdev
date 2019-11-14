<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KaizenForm extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'employee_id', 'employee_name', 'propose_date', 'section', 'sub_leader', 'title', 'condition', 'improvement'
	];

}
