<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OriginGroup extends Model
{
	use SoftDeletes;
    //
	protected $fillable = [
		'origin_group_code', 'origin_group_name', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
    //
}
