<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class area_inspection extends Model
{
	use SoftDeletes;
	public $table = "area_inspection";
    //
	protected $fillable = [
		'id','area'
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
    //
}
