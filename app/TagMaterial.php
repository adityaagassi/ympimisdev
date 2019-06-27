<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagMaterial extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'tag', 'material_number', 'quantity', 'op_prod', 'location', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
