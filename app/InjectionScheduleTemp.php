<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionScheduleTemp extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'date','due_date','material_number','material_description','part','color', 'stock','plan','diff','debt','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
