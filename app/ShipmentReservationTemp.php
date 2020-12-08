<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentReservationTemp extends Model{

	protected $fillable = [
		'period',
		'stuffing',
		'bl_date',
		'port_of_delivery',
		'country',
		'created_by'
	];

}
