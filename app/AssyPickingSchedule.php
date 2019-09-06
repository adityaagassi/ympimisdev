<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssyPickingSchedule extends Model
{
    Use SoftDeletes;

    protected $fillable = [
		'material_number', 'due_date', 'quantity', 'created_by'
	];
}
