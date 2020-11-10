<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GeneralShoesRequest extends Model{

	use SoftDeletes;
	
	protected $fillable = [
		'request_id','employee_id','merk','gender','size','quantity','created_by'
	];

}
