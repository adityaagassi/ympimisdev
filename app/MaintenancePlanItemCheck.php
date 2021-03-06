<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlanItemCheck extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'id','item_code', 'itm_check', 'period', 'check', 'check_value', 'check_after', 'photo_before', 'photo_after', 'remark', 'created_by'
    ];
}
