<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapacityPartInjection extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'rfid', 'gmc', 'part_name', 'part_type', 'color', 'capacity','created_by'
	];
}
