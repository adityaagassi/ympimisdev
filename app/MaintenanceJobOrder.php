<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceJobOrder extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'order_no', 'section', 'priority', 'type', 'category', 'machine_name', 'machine_no', 'machine_condition', 'danger', 'description', 'target_date', 'safety_note', 'remark', 'note', 'created_by'
    ];
}
