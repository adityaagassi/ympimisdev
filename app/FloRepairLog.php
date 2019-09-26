<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FloRepairLog extends Model
{
	protected $fillable = [
		'serial_number', 'material_number', 'origin_group_code', 'flo_number', 'quantity', 'status', 'packed_at'
	];
}
