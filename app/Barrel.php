<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barrel extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'machine', 'tag', 'material_number', 'qty', 'status', 'finish_racking', 'remark', 'created_by', 'key'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
