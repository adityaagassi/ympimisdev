<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsenceCategory extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'absence_code', 'absence_name', 'deduction', 'ration', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
