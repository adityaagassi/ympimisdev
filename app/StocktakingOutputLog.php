<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StocktakingOutputLog extends Model{

	use SoftDeletes;

	protected $fillable = [
		'material_number', 'store', 'location', 'quantity', 'stocktaking_date' 
	];

}
