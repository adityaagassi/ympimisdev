<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionMachineCycleTime extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'part','model','cycle', 'shoot','qty','machine','color','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
