<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrQuestionDetail extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'message','created_by','message_id'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
