<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccPaymentRequest extends Model
{
    protected $fillable = [
		'payment_date','supplier_code','supplier_name','currency','payment_term','payment_due_date','amount','kind_of','attach_document','file','pdf','posisi','status','manager','manager_name','status_manager','dgm','status_dgm','gm','status_gm','created_by','created_name'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
