<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccBudgetHistory extends Model
{	
    protected $fillable = [	
		'budget','budget_month','budget_date','category_number','no_item','beg_bal','amount','budget_month_po','po_number','amount_po','budget_month_receive','amount_receive','status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}