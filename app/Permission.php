<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'role_code', 'navigation_code', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function navigation()
	{
		return $this->belongsTo('App\Navigation', 'navigation_code', 'navigation_code')->withTrashed();
	}
}
