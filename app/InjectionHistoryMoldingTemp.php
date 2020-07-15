<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionHistoryMoldingTemp extends Model
{
    protected $fillable = [
		'type','pic','mesin', 'part', 'color','total_shot','start_time','end_time','note','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
