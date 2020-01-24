<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PingLog extends Model
{
    protected $fillable = [
		'ip', 'remark', 'time', 'status', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
