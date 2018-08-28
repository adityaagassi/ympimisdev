<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
	use SoftDeletes;
    //
    protected $fillable = [
        'destination_code', 'destination_name', 'created_by'
    ];
    
    public function user()
    {
    	return $this->belongsTo('App\User', 'created_by');
    }
}
