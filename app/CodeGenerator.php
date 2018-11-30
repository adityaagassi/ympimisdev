<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeGenerator extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'prefix', 'length', 'index', 'note', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}