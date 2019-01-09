<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlcCounter extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'origin_group_code', 'plc_counter', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}