<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JigKensaLog extends Model
{
    use softDeletes;

	protected $fillable = [
		'jig_id','jig_index', 'jig_child','jig_alias', 'check_index', 'check_name', 'upper_limit', 'lower_limit','value','result','status','started_at','finished_at','operator_id', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
