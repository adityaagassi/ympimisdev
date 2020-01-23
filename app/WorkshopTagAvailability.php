<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopTagAvailability extends Model{
    use SoftDeletes;

	protected $fillable = [
		'tag', 'remark', 'status'
	];
}
