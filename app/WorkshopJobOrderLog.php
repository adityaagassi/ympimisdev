<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class WorkshopJobOrderLog extends Model{

    use SoftDeletes;
	protected $fillable = [
		'order_no', 'remark', 'created_by' 
	];
}
