<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccBudgetHistory extends Model
{	
    protected $fillable = [	
		'budget','budget_month','budget_date','category','category_number','no_item','amount','status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}