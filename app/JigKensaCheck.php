<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JigKensaCheck extends Model
{
    use softDeletes;

	protected $fillable = [
		'jig_id','jig_child','jig_alias', 'check_index', 'check_name', 'upper_limit', 'lower_limit', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
