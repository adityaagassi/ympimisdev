<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionHistoryMoldingTemp extends Model
{
    protected $fillable = [
		'molding_code','type','pic','mesin', 'part', 'color','total_shot','start_time','end_time','note','remark','reason','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
