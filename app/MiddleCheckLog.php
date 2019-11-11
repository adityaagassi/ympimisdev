<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MiddleCheckLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id','tag','material_number','quantity','location','operator_id','buffing_time'
	];

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}
}