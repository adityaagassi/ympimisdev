<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
	use SoftDeletes;

	protected $fillable = [
        'status_code', 'status_name', 'created_by'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
    //
}
