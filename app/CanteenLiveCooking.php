<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanteenLiveCooking extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'order_by','order_for','due_date', 'status', 'approved_by','approved_at','remark', 'created_by'
	];
}