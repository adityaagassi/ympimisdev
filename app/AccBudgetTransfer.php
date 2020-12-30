<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccBudgetTransfer extends Model
{
	use SoftDeletes;
	
	protected $fillable = [	
		'request_date','budget_from','budget_to','amount','note','approval_f','approval_from','date_approval_from','approval_t','approval_to','date_approval_to','posisi','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

}
