<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionHistoryMoldingLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'molding_code','type','pic','mesin', 'part', 'color','total_shot','start_time','end_time','running_time','note','decision','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
