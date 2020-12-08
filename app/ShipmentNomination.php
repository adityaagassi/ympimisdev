<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentNomination extends Model{

	protected $fillable = [
		'ship_id',
		'shipper',
		'port_loading',
		'consignee',
		'transship_port',
		'port_of_discharge',
		'port_of_delivery',
		'country',
		'carier',
		'nomination',
		'created_by'
	];

}
