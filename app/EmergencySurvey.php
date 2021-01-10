<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmergencySurvey extends Model
{
    protected $fillable = [
		'employee_id',  'answer', 'relationship', 'family_name'
	];
}
