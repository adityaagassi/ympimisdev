<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StocktakingMaterialForecast extends Model{
	
	protected $fillable = [
		'material_number', 'created_by' 
	];

}
