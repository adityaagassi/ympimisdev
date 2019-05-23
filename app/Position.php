<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id', 'position'
	];

	// public function user()
	// {
	// 	return $this->belongsTo('App\User', 'created_by')->withTrashed();
	// }
}
