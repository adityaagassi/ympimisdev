<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiddleGroup extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'group_name', 'pic', 'remark', 'ip', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
