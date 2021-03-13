<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccActual extends Model
{
    use SoftDeletes;
	
    protected $fillable = [	
		'budget_no','document_no','receive_date','currency','vendor_code','vendor_name','invoice_no','no_po_sap','no_urut','no_po','category','item_no','item_description','uom','qty','price','amount','amount_dollar','gl_number','gl_description','cost_center','cost_description','pch_code','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
