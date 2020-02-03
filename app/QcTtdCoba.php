<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcTtdCoba extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'id','cpar_no','ttd','ttd_car','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
