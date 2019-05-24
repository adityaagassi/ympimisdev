<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakTime extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'day', 'start', 'end', 'duration', 'shift', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
