<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisAudit extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'audit_date', 'pic', 'remark', 'created_by'
    ];
}
