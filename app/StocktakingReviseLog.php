<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StocktakingReviseLog extends Model{

	protected $fillable = [
		'st_id',
		'location',
		'store',
		'sub_store',
		'material_number',
		'category',
		'before',
		'final_count',
		'revised_by',
		'reason'
	];

}
