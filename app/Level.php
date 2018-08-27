<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
	use SoftDeletes;

	 protected $fillable = [
        'level_name', 'created_by'
    ];
    //
    public function user()
    {
		return $this->belongsTo('App\User', 'created_by');
    }

}
