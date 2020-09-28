<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePic extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'item_name', 'pic_id', 'pic_name', 'skill', 'remark', 'created_by'
	];
}
