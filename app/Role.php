<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'role_code', 'role_name', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function permissions()
	{
		return $this->hasMany('App\Permission', 'role_code', 'role_code')->withTrashed();
	}

	public function role()
	{
		return $this->belongsTo('App\Role', 'role_code', 'role_code')->withTrashed();
	}
    //
}
