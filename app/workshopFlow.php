<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class workshopFlow extends Model
{
    use SoftDeletes;
	protected $fillable = [
		'flow_name', 'category', 'process_number', 'flow_process', 'duration', 'created_by'
	];
}
