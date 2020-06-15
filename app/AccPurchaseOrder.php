<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccPurchaseOrder extends Model
{
    protected $fillable = [
		'no_po','tgl_po','supplier','material','vat','transportation','delivery_term','holding_tax','currency','buyer_id','buyer_name','note','autorized2','approval_autorized2','date_approval_autorized2','autorized3','approval_autorized3','date_approval_autorized3','reject','datereject','posisi','status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
