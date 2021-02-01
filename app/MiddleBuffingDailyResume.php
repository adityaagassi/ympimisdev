<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleBuffingDailyResume extends Model {
	
	protected $fillable = [
		'date', 'location', 'remark', 'check', 'ng'
	];

}
