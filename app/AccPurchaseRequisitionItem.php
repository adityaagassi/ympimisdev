<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccPurchaseRequisitionItem extends Model
{
    protected $fillable = [
		'no_pr','item_code','item_desc','item_spec','item_stock','item_request_date','item_qty','item_uom','item_currency','item_price','item_amount','no_budget','sudah_po','status_harga','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}


    public function pr()
    {
        return $this->belongsTo('App\AccPurchaseRequisition', 'no_pr', 'no_pr')->withTrashed();
    }
}

