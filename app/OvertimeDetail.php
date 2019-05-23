<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeDetail extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'overtime_id', 'employee_id', 'cost_center', 'food', 'ext_food', 'start_time', 'end_time', 'purpose', 'remark', 'final_hour', 'final_overtime', 'status', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
