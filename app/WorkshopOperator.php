<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopOperator extends Model{
    use SoftDeletes;

	protected $fillable = [
		'operator_id', 'task', 'created_by'
	];
}
