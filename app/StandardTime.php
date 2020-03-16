<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StandardTime extends Model{

	protected $fillable = [
		'material_number', 'process', 'location', 'time', 'created_by' 
	];

}
