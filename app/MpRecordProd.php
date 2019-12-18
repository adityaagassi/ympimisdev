<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MpRecordProd extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'date','pic','shift', 'product','material_number','process','machine','punch_number','die_number','start_time','end_time','lepas_molding','pasang_molding','process_time','electric_supply_time','data_ok','punch_value','die_value','note','created_by'
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
