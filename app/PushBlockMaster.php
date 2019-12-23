<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushBlockMaster extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'type', 'no_cavity','cavitiy_1','cavitiy_2','cavitiy_3','cavitiy_4','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
