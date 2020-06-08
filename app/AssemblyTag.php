<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyTag extends Model
{
    protected $fillable = [
		'tag', 'serial_number', 'model', 'origin_group_code','created_by'
	];
}