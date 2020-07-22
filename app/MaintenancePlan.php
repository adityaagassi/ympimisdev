<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlan extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'item_check', 'quantity', 'category', 'status', 'schedule', 'pic', 'remark', 'fiscal', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember', 'januari', 'februari', 'maret', 'created_by'
    ];
}
