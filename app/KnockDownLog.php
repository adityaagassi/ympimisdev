<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class KnockDownLog extends Model{
	use SoftDeletes;

	protected $fillable = [
		'kd_number', 'status', 'created_by', 'updated_at'
	];
}
