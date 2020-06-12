<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssemblyFlow extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'flow', 'process','origin_group_code','created_by'
	];
}
