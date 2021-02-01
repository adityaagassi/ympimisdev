<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleLacqueringDailyResume extends Model {
	
	protected $fillable = [
		'date', 'location', 'remark', 'check', 'ng'
	];

}
