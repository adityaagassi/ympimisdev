<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceJobSparepart extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'order_no', 'part_number', 'quantity', 'created_by'
	];
}
