<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanteenLiveCookingAdmin extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id','live_cooking_role','department','section','order_quota','remark', 'created_by'
	];
}
