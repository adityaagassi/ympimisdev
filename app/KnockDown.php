<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnockDown extends Model{
	use SoftDeletes;

	protected $fillable = [
		'kd_number', 'max_count', 'actual_count', 'remark', 'status', 'closure_id', 'invoice_number', 'container_id', 'marking', 'created_by'
	];
}
