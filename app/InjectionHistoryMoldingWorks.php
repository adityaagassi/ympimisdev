<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionHistoryMoldingWorks extends Model
{
    protected $fillable = [
		'molding_code','type','pic','mesin', 'part','start_time','end_time','reason','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
