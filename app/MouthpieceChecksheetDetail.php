<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MouthpieceChecksheetDetail extends Model
{
	use SoftDeletes;
	
	protected $fillable = [	
		'kd_number','material_number','material_description','quantity','actual_quantity','remark','created_by','end_picking', 'employee_id'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
