<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MesinLogInjection extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'activity_name', 'activity_alias', 'frequency', 'department_id', 'activity_type'
	];
}
