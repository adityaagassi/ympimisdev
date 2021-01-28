<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IvmsTemperatureTemp extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'person_id','employee_id','name','department','section','group','location', 'date_in','date', 'point','temperature','abnormal_status','shiftdaily_code','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
