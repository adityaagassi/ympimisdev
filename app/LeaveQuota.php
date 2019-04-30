<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveQuota extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'employeed', 'leave_quota', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
