<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionSchedule extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'material_number', 'destination_code', 'due_date', 'quantity', 'created_by'
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
	public function weeklycalendar()
	{
		return $this->belongsTo('App\WeeklyCalendar', 'due_date', 'week_date')->withTrashed();
	}
    //
}
