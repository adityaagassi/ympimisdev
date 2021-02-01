<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleBuffingMonthlyNgResume extends Model {
	
	protected $fillable = [
		'fiscal_year', 'month', 'location', 'hpl', 'key', 'ng_name', 'remark', 'ng'
	];

}
