<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyReport extends Model
{
  use SoftDeletes;
  
  protected $fillable = [
		'report_code','category','description','location','duration', 'begin_date', 'target_date', 'finished_date', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
