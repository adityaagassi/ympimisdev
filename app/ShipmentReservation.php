<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentReservation extends Model{

	protected $fillable = [
		'period',
		'ycj_ref_number',
		'help',
		'status',
		'shipper',
		'port_loading',
		'port_of_discharge',
		'country',
		'port_of_delivery',
		'carier',
		'nomination',
		'fortyhc',
		'forty',
		'twenty',
		'booking_number',
		'stuffing_date',
		'etd_date',
		'application_rate',
		'plan_teus',
		'plan',
		'remark',
		'due_date',
		'invoice_number',
		'ref',
		'actual_stuffing',
		'actual_on_board',
		'actual_departed',
		'created_by'
	];

}
