<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionProcessTemp extends Model
{
    protected $fillable = [
		'tag_product','tag_molding','operator_id','start_time', 'mesin','material_number','part_name', 'part_type','color','cavity','molding','shot','dryer','dryer_lot_number','dryer_color','ng_name','ng_count','remark','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
