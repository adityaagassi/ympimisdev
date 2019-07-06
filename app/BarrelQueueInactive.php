<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarrelQueueInactive extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'tag', 'material_number', 'remark', 'quantity'
	];

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}
}
