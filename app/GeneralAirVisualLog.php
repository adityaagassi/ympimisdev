<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralAirVisualLog extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'get_at', 'co', 'temperature', 'humidity', 'location', 'remark', 'data_time'
	];
}
