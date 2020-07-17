<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceInventory extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'part_number', 'part_name', 'location', 'stock', 'uom', 'created_by'
	];
}
