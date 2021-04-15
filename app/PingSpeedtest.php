<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PingSpeedtest extends Model
{
    protected $fillable = [
		'download','upload','ping','city','country','address','service_provider','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
