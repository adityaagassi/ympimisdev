<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnInventorie extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'line','tag','model','location','qty','status','created_by'
    ];

    	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
