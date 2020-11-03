<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JigRepair extends Model
{

	protected $fillable = [
		'jig_id','jig_index','jig_child','jig_alias', 'check_index','check_name','lower_limit','upper_limit','value','result','status','action','started_at','finished_at','operator_id', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
