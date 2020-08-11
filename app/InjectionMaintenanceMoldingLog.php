<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionMaintenanceMoldingLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'pic','product','part', 'mesin', 'last_counter','status','start_time','end_time','running_time','note','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
