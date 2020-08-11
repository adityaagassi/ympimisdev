<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionMaintenanceMoldingTemp extends Model
{
    protected $fillable = [
		'pic','product','part', 'mesin', 'last_counter','status','start_time','end_time','note','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
