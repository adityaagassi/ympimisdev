<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanteenLiveCookingMenu extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'periode','due_date','menu_name','menu_image','serving_quota','serving_ordered','remark', 'created_by'
	];
}
