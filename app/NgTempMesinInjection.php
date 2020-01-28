<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NgTempMesinInjection extends Model
{

	protected $fillable = [
		'mesin','rfid','rfid_molding','pic','part_name','part_code','color','capacity','start_time','running_shot','ng_name','ng_count','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
