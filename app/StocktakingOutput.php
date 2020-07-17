<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class StocktakingOutput extends Model{

	protected $fillable = [
		'material_number', 'store', 'location', 'quantity' 
	];

}
