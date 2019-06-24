<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarrelLog extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'machine', 'tag', 'material_number', 'qty', 'status', 'duration', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
