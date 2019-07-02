<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PnOperator extends Model
{
    use SoftDeletes;

       protected $fillable = [
        'nik','nama','bagian','tag','line','created_by'
    ];

    	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
