<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolderingStandardTime extends Model{
	protected $connection = 'welding_controller';
	protected $table = 'ympimis.standard_times';

	protected $fillable = [
		'material_number', 'process', 'location', 'time', 'created_by' 
	];
}
