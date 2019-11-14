<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanMesinInjectionTmp extends Model
{
   use SoftDeletes;

	protected $fillable = [
		'mesin', 'part', 'qty', 'color', 'due_date', 'created_by'
	];
}
