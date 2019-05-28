<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendEmail extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'email', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
