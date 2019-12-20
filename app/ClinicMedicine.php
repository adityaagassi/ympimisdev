<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClinicMedicine extends Model{
	use SoftDeletes;

	protected $fillable = [
		'medicine_name', 'quantity'
	];

}
