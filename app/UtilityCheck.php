<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityCheck extends Model
{
    Use SoftDeletes;

    protected $fillable = [
		'utility_id', 'check', 'check_date', 'remark', 'created_by'
	];
}
