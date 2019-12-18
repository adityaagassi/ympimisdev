<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MpTroubleLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'date','pic','shift', 'material_number','process','machine','start_time','end_time','reason','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
