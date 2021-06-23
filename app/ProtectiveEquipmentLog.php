<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProtectiveEquipmentLog extends Model{
	
	use SoftDeletes;

	protected $fillable = [
		'operator_id', 'apd_name', 'location', 'quantity', 'leader'
	];

}
