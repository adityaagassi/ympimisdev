<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddlePlatingDailyResume extends Model {
	
	protected $fillable = [
		'date', 'location', 'remark', 'check', 'ng'
	];

}

