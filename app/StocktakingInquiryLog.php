<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StocktakingInquiryLog extends Model{

	use SoftDeletes;

	protected $fillable = [
		'store', 'category', 'material_number', 'location', 'quantity', 'stocktaking_date' 
	];

}