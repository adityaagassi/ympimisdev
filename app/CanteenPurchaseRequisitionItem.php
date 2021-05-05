<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanteenPurchaseRequisitionItem extends Model
{
    protected $fillable = [
		'no_pr','item_code','item_desc','item_stock','item_request_date','item_qty','item_uom','item_currency','item_price','item_amount','sudah_po','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
