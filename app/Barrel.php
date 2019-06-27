<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barrel extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'machine', 'jig', 'tag', 'material_number', 'qty', 'status', 'finish_racking', 'finish_queue', 'remark', 'created_by', 'key'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
