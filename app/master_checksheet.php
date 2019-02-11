<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class master_checksheet extends Model
{
	use SoftDeletes;
	public $table = "master_checksheet";
    //
	protected $fillable = [
		'check_by','status','id_input','id_checkSheet','countainer_number', 'destination', 'invoice', 'seal_number', 'etd_sub', 'payment', 'carier', 'shipped_from', 'shipped_to', 'Stuffing_date','created_by'
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
    //
}
