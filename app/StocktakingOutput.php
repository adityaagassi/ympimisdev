<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StocktakingOutput extends Model{
	use SoftDeletes;

	protected $fillable = [
		'material_number', 'store', 'location', 'quantity' 
	];

}
