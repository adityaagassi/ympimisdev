<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContainerSchedule extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'container_id', 'container_code', 'destination_code', 'shipment_date', 'container_number', 'att', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
	public function destination()
	{
		return $this->belongsTo('App\Destination', 'destination_code', 'destination_code')->withTrashed();
	}
	public function container()
	{
		return $this->belongsTo('App\Container', 'container_code', 'container_code')->withTrashed();
	}
	public function weeklycalendar()
	{
		return $this->belongsTo('App\WeeklyCalendar', 'shipment_date', 'week_date')->withTrashed();
	}
    //
}
