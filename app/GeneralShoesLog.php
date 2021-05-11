<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GeneralShoesLog extends Model{

	use SoftDeletes;
	
	protected $fillable = [
		'merk',
		'gender',
		'size',
		'quantity',
		'status',
		'metode',
		'condition',
		'employee_id',
		'name',
		'department',
		'section',
		'group',
		'sub_group',
		'requested_by',
		'created_by'
	];

}
