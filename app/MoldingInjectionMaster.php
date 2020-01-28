<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoldingInjectionMaster extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'rfid','product','part', 'mesin', 'last_counter','status','status_mesin','ng_count','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
