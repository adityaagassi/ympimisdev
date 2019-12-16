<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ShipmentSchedule extends Model
{
	use SoftDeletes;
    //
	protected $fillable = [
		'st_month', 'sales_order', 'shipment_condition_code', 'destination_code', 'material_number', 'hpl', 'bl_date', 'st_date', 'quantity' , 'actual_quantity', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function destination()
	{
		return $this->belongsTo('App\Destination', 'destination_code', 'destination_code')->withTrashed();
	}

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}

	public function volume()
	{
		return $this->belongsTo('App\MaterialVolume', 'material_number', 'material_number')->withTrashed();
	}

	public function weeklycalendar()
	{
		return $this->belongsTo('App\WeeklyCalendar', 'st_date', 'week_date')->withTrashed();
	}

	public function shipmentcondition()
	{
		return $this->belongsTo('App\ShipmentCondition', 'shipment_condition_code', 'shipment_condition_code')->withTrashed();
	}
    //
}
