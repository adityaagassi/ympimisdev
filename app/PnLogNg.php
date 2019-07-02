<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnLogNg extends Model
{
   use SoftDeletes;
    
    protected $fillable = [
        'ng','line','operator','tag','model','location','created_by','qty'
    ];

    	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
