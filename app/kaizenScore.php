<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class kaizenScore extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'id_kaizen', 'foreman_point_1', 'foreman_point_2', 'foreman_point_3', 'manager_point_1', 'manager_point_2', 'manager_point_3', 'created_by'
	];
}
