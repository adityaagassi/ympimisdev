<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleBuffingWeeklyResume extends Model {
	
	protected $fillable = [
		'fiscal_year', 'month', 'week', 'location', 'remark', 'check', 'ng'
	];

}
