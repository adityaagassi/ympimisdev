<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccPurchaseOrder extends Model
{
    protected $fillable = [
		'no_po','tgl_po','supplier','supplier_due_payment','supplier_status','material','vat','transportation','delivery_term','holding_tax','currency','buyer_id','buyer_name','note','authorized2','approval_authorized2','date_approval_authorized2','authorized3','approval_authorized3','date_approval_authorized3','reject','datereject','posisi','status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
