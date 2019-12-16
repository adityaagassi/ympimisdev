<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class KnockDownDetail extends Model{
	use SoftDeletes;

	protected $fillable = [
		'kd_number', 'material_number', 'quantity', 'shipment_schedule_id', 'storage_location', 'created_by'
	];
}
