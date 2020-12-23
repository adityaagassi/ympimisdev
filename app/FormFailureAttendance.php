<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormFailureAttendance extends Model
{
    protected $fillable = [
		'form_id','employee_tag','employee_id','attend_time','status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
