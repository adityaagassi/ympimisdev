<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccInvoice extends Model
{
    protected $fillable = [
		'invoice_date','supplier_code','supplier_name','kwitansi','invoice_no','surat_jalan','bap','npwp','faktur_pajak','po_number','payment_term','currency','amount','do_date','due_date','distribution_date','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
