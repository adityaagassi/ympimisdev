<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionDryerLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'dryer','material_number','material_description','part','color','qty','lot_number','type','employee_id','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
