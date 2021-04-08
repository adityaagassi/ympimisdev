<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PingNetworkUsageLog extends Model
{
     protected $fillable = [
		'hostname','ip','uptime','last_boot','remark', 'received', 'sent', 'err', 'drop', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
