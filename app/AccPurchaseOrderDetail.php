<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccPurchaseOrderDetail extends Model
{
    protected $fillable = [
		'no_po','no_pr','no_item','nama_item','budget_item','delivery_date','qty','uom','goods_price','last_price','service_price','konversi_dollar','gl_number','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
