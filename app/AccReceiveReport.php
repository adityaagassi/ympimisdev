<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccReceiveReport extends Model
{
    protected $fillable = [
		'id_print','no_po','no_item','nama_item','qty_receive','date_receive','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
