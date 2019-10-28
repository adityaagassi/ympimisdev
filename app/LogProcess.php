<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogProcess extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'process_code', 'serial_number', 'model', 'manpower', 'created_by', 'origin_group_code', 'created_at','remark'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function process()
	{
		return $this->belongsTo('App\Process', 'process_code', 'process_code')->withTrashed();
	}
    //
}
