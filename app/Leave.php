<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'employee_id', 'acquisition_date', 'leave_quota', 'leave_left', 'valid_from', 'valid_to', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function employee()
	{
		return $this->belongsTo('App\User', 'employee_id')->withTrashed();
	}
}