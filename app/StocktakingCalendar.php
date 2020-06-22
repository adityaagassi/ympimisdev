<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StocktakingCalendar extends Model{

	use SoftDeletes;

	protected $fillable = [
		'date', 'activity', 'status', 'created_by' 
	];

}