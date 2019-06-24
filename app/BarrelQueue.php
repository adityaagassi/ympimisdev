<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarrelQueue extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'tag', 'material_number', 'remark', 'quantity'
	];

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}

	public function middle_group()
	{
		return $this->belongsTo('App\MiddleGroup', 'group_id')->withTrashed();
	}
}