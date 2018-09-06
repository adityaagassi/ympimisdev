<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyCalendar extends Model
{
    use SoftDeletes;
    //
	protected $fillable = [
		'fiscal_year', 'week_name', 'week_date', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
