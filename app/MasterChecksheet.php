<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MasterChecksheet extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'no_pol','check_by','status','id_input','id_checkSheet','countainer_number', 'destination', 'invoice', 'seal_number', 'etd_sub', 'payment', 'carier', 'shipped_from', 'shipped_to', 'Stuffing_date','created_by','reason','finish_stuffing','start_stuffing','invoice_date','toward', 'ct_size', 'ycj_ref_number', 'period', 'driver_name', 'driver_photo', 'seal_photo', 'container_photo' 
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function destination()
	{
		return $this->belongsTo('App\Destination', 'destination_code', 'destination_code')->withTrashed();
	}

	public function shipmentcondition()
	{
		return $this->belongsTo('App\ShipmentCondition', 'carier', 'shipment_condition_code')->withTrashed();
	}

	public function user2()
	{
		return $this->belongsTo('App\User', 'created_by','id')->withTrashed();
	}

	public function user3()
	{
		return $this->belongsTo('App\User', 'check_by','id')->withTrashed();
	}
}
