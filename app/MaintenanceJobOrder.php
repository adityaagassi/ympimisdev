<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceJobOrder extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'order_no', 'section', 'priority', 'type', 'category', 'machine_name', 'machine_remark', 'machine_condition', 'danger', 'description', 'target_date', 'safety_note', 'remark', 'note', 'att', 'created_by'
    ];
}
