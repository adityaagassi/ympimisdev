<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopProcess extends Model{
    use SoftDeletes;

	protected $fillable = [
		'machine_code', 'tag', 'machine_name', 'process_name', 'short_name', 'skill_name', 'area_name', 'created_by'
	];
}
