<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionScheduleAdjustmentLog extends Model
{

    protected $fillable = [
		'id_schedule','machine_from','machine_to','material_number','material_description','part','color', 'qty','start_time_from','end_time_from','start_time_to','end_time_to','reason','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
