<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccPurchaseOrderDetail extends Model
{
    protected $fillable = [
		'no_po','no_pr','delivery_date','no_item','qty','goods','last_price','services','po_date','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
