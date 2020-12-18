<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StocktakingNewList extends Model{
	use SoftDeletes;

	protected $fillable = [
		'location',
		'store',
		'sub_store',
		'material_number',
		'category',
		'process',
		'print_status',
		'remark',
		'quantity',
		'inputed_by',
		'inputed_at',
		'audit1',
		'audit1_by',
		'audit1_at',
		'final_count',
		'revised_by',
		'revised_at',
		'reason',
		'created_by'
	];
}
