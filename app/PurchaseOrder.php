<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'purchdoc', 'order_no', 'order_date', 'pgr', 'pgr_name', 'rev_no', 'rev_date', 'vendor', 'name', 'street', 'city', 'postl_code', 'cty', 'salesperson', 'sc', 'sc_name', 'tpay', 'tpay_name', 'telephone', 'fax_number', 'incot', 'curr', 'item', 'material', 'description', 'deliv_date', 'order_qty', 'base_unit_of_measure', 'price', 'amount', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
