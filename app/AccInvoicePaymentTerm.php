<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccInvoicePaymentTerm extends Model
{
    protected $fillable = [
		'payment_term','payment_term_day','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
