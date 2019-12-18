<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MpTroubleLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'date','pic','shift','product','shift', 'material_number','process','machine','start_time','end_time','reason','created_by'
	];

	public function employee_pic()
    {
        return $this->belongsTo('App\Employee', 'pic', 'employee_id')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
