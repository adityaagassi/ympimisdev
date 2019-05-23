<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutationLog extends Model
{
	use SoftDeletes;
	
	protected $fillable = [
		'employee_id', 'cost_center', 'division','department', 'section', 'sub_section','group', 'valid_from', 'valid_to','created_by','reason'
	];

	function employee() {
		return $this->belongsTo('App\Employee');
	}
}
