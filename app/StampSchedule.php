<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StampSchedule extends Model
{
	use SoftDeletes;
    //
	protected $fillable = [
		'model', 'due_date', 'quantity', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
