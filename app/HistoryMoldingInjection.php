<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryMoldingInjection extends Model
{
	use SoftDeletes;

    protected $fillable = [
		'type','pic','mesin', 'part', 'color','total_shot','start_time','end_time','running_time','note','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
