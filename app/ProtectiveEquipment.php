<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProtectiveEquipment extends Model{
	
	use SoftDeletes;

	protected $fillable = [
		'apd_name', 'location', 'quantity'
	];


}
