<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionScheduleLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'id_schedule','machine','material_number','material_description','part','color', 'qty','start_time','end_time','reason','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
