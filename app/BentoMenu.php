<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BentoMenu extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'due_date', 'menu_image', 'remark', 'created_by'
	];
}
