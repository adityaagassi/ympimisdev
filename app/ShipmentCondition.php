<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentCondition extends Model
{
	use SoftDeletes;
    //
	protected $fillable = [
		'shipment_condition_code', 'shipment_condition_name', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
    //
}
