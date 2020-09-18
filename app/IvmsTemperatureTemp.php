<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IvmsTemperatureTemp extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'person_id','employee_id','name','location', 'date_in','date', 'point','temperature','abnormal_status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
