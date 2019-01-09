<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StampInventory extends Model
{
	use SoftDeletes;
    //
	protected $fillable = [
		'process_code', 'origin_group_code', 'model', 'quantity', 'serial_number'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
