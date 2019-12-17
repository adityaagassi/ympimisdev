<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnockDown extends Model{
	use SoftDeletes;

	protected $fillable = [
		'kd_number', 'created_by', 'max_count', 'actual_count', 'remark', 'status', 'invoice_number', 'container_id'
	];
}
