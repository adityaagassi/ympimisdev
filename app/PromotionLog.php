<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromotionLog extends Model
{
	use softDeletes;

	protected $fillable = [
		'employee_id', 'grade_code', 'grade_name','position','valid_from','valid_to', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
