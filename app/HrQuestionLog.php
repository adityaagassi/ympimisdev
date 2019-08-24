<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrQuestionLog extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'message','category','created_by','updated_at'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
