<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KaizenCalculation extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'id_kaizen', 'id_cost', 'cost', 'created_by','created_at'
	];
}
