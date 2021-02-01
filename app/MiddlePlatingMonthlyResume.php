<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddlePlatingMonthlyResume extends Model {
	
	protected $fillable = [
		'fiscal_year', 'month', 'location', 'remark', 'check', 'ng'
	];

}
