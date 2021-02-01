<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleLacqueringMonthlyResume extends Model {
	
	protected $fillable = [
		'fiscal_year', 'month', 'location', 'remark', 'check', 'ng'
	];

}

