<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
  	use SoftDeletes;

	protected $fillable = [
		'employee_id', 'name', 'gender', 'family_id', 'birth_place', 'birth_date','address','phone','wa_number','card_id','account','bpjstk','jp','bpjskes','npwp','direct_superior','hire_date','end_date','avatar','remark','created_by'
	];

}
