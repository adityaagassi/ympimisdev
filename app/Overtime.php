<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Overtime extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'overtime_id', 'overtime_date', 'day_status','remark','shift', 'division', 'department', 'section', 'subsection', 'group', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
