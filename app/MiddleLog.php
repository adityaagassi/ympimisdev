<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiddleLog extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'tag', 'material_number', 'location', 'quantity', 'operator', 'remark'
	];

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}
}
