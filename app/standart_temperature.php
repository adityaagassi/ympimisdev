<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class standart_temperature extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'id','lokasi','upper_limit','lower_limit'
	];
}
