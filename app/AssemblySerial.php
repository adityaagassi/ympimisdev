<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssemblySerial extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'serial_number','origin_group_code','created_by'
	];
}
