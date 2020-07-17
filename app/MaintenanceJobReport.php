<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceJobReport extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'order_no', 'operator_id', 'cause', 'handling', 'spare_part', 'photo', 'remark', 'created_by'
    ];
}
