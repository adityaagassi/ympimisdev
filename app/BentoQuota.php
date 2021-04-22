<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BentoQuota extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'due_date', 'serving_quota', 'serving_ordered', 'remark', 'remark', 'created_by'
	];
}
