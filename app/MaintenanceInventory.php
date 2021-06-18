<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceInventory extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'item_number',
		'part_name',
		'category',
		'specification',
		'maker',
		'location',
		'min_stock',
		'max_stock',
		'uom',
		'user','cost', 'created_by'
	];
}
