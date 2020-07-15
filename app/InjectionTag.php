<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionTag extends Model
{
    protected $fillable = [
		'tag', 'operator_id','material_number','part_name', 'part_type', 'part_code', 'color','cavity', 'location', 'shot', 'availability','height_check','push_pull_check','torque_check', 'remark','created_by'
	];
}
