<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class st_assemblies extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'origin_group_code', 'model', 'process_code' , 'st','created_by'
	];

		public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function origingroup()
	{
		return $this->belongsTo('App\OriginGroup', 'origin_group_code', 'origin_group_code')->withTrashed();
	}

	public function Process()
	{
		return $this->belongsTo('App\Process', 'process_code', 'process_code')->withTrashed();
	}
    //
}