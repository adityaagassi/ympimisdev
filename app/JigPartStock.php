<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JigPartStock extends Model
{
    use softDeletes;

	protected $fillable = [
		'jig_id','quantity', 'min_stock', 'min_order', 'quantity_order','material', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
