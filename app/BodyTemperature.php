<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BodyTemperature extends Model
{
    use SoftDeletes;
	protected $fillable = [
		'company','name','suhu','created_by'
	];
}
