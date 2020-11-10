<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionMachineWork extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'mesin','material_number','part_name', 'part_type','color','cavity','molding','start_time','tag_molding','shot','dryer','dryer_lot_number','dryer_color','ng_name','ng_count','remark','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
