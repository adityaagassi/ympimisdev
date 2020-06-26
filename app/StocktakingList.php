<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StocktakingList extends Model{

	use SoftDeletes;

	protected $fillable = [
		'store', 'category', 'material_number', 'location', 'remark', 'process', 'quantity', 'audit1', 'audit2', 'final_count', 'inputed_by', 'audit1_by', 'audit2_by', 'reason', 'created_by' 
	];

}
