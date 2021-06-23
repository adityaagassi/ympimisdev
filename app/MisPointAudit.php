<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisPointAudit extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'system_name', 'location', 'department', 'item_check', 'remark', 'created_by'
    ];
}
