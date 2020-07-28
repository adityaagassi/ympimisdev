<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccInvestment extends Model
{
    protected $fillable = [
		'applicant_id','applicant_name','applicant_department','reff_number','submission_date','category','subject','subject_jpy','type','objective','objective_detail','objective_detail_jpy','supplier_code','supplier_name','pkp','npwp','certificate','delivery_order','date_order','payment_term','note','quotation_supplier','budget_category','budget_no','currency','file','posisi','status','pdf','ycj_approval','total','service','vat','bukti_adagio','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}


