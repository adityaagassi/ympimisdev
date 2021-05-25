<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccPaymentRequest extends Model
{
    protected $fillable = [
		'submission_date','supplier_code','supplier_name','currency','payment_term','due_date','amount','kind_of','attach_document','file','created_by','created_name'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
