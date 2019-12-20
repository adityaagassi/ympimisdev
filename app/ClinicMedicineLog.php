<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClinicMedicineLog extends Model{
	use SoftDeletes;

	protected $fillable = [
		'medicine_name', 'status', 'clinic_visitor_detail', 'quantity'
	];
}
