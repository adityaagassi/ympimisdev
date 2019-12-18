<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MpKanagataLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'date','pic','shift','material_number','process','machine','punch_number','die_number','punch_value','die_value','punch_total','die_total','start_time','end_time','note','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
