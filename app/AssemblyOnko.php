<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssemblyOnko extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'key', 'nomor','created_by'
	];
}
