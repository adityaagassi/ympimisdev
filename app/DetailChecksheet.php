<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DetailChecksheet extends Model
{
	use SoftDeletes;
	
    protected $fillable = [
		'bara','diff','confirm','id_checkSheet','countainer_number', 'gmc', 'goods', 'marking', 'package_qty', 'package_set', 'qty_qty', 'qty_set', 'created_by', 'destination','invoice'
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
}
