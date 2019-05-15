<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoListFile extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'order_no', 'file_name', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo(App\User, created_by)->withTrashed();
	}
}
