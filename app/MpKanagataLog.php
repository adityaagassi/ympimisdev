<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MpKanagataLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'date','pic','shift','product','shift','material_number','process','machine','punch_number','die_number','punch_value','die_value','punch_total','die_total','start_time','end_time','note','created_by'
	];

	public function employee_pic()
    {
        return $this->belongsTo('App\Employee', 'pic', 'employee_id')->withTrashed();
    }

    public function material()
    {
        return $this->belongsTo('App\MpMaterial', 'material_number', 'material_number')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
