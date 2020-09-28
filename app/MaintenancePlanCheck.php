<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlanCheck extends Model
{
    Use SoftDeletes;

    protected $fillable = [
		'item_code', 'part_check', 'item_check', 'substance', 'check', 'check_value', 'remark', 'created_by'
	];
}
