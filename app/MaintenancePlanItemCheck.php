<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlanItemCheck extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'item_code', 'part', 'itm_check', 'essay_category', 'substance', 'remark', 'created_by'
    ];
}
