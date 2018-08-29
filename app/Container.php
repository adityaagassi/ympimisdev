<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'container_code', 'container_name', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by');
	}
    //
}
