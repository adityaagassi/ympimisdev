<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBeacons extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'id','uuid','lokasi','distance'
	];
}
