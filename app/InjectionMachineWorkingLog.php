<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionMachineWorkingLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'mesin','status','reason','material_number','color','cavity','molding','dryer','dryer_lot_number','dryer_color','start_time','end_time','remark','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
