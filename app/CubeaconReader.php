<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CubeaconReader extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'uuid','name','created_by'
	];
}
