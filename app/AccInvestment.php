<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccInvestment extends Model
{
    protected $fillable = [
		'applicant_id','applicant_name','applicant_department','reff_number','submission_date','category','subject','type','objective','objective_detail','desc_supplier','desc_pkp','desc_npwp','desc_certificate','currency','delivery_order','date_order','payment_term','quotation_supplier','budget_no','file','posisi','status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}


