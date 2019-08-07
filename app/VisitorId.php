<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VisitorId extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'full_name','ktp','telp'
	];
}
