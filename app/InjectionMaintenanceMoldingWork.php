<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionMaintenanceMoldingWork extends Model
{
    protected $fillable = [
		'maintenance_code','pic','product','part', 'last_counter','status','start_time','end_time','reason','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
