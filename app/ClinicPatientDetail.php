<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClinicPatientDetail extends Model{
	use SoftDeletes;

	protected $fillable = [
		'employee_id', 'purpose', 'diagnose', 'paramedic', 'doctor', 'family', 'family_name', 'visited_at'
	];
}
