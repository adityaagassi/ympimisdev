<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanteenLiveCooking extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'order_by','order_for','due_date', 'status', 'whatsapp_status','remark', 'created_by'
	];
}