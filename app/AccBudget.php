<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccBudget extends Model
{
	use SoftDeletes;
	
    protected $fillable = [	
		'periode','budget_no','department','description','amount','env','purpose','pic','account_name','category','apr_budget_awal','may_budget_awal','jun_budget_awal','jul_budget_awal','aug_budget_awal','sep_budget_awal','oct_budget_awal','nov_budget_awal','dec_budget_awal','jan_budget_awal','feb_budget_awal','mar_budget_awal','adj_frc','apr_after_adj','may_after_adj','jun_after_adj','jul_after_adj','aug_after_adj','sep_after_adj','oct_after_adj','nov_after_adj','dec_after_adj','jan_after_adj','feb_after_adj','mar_after_adj','apr_sisa_budget','may_sisa_budget','jun_sisa_budget','jul_sisa_budget','aug_sisa_budget','sep_sisa_budget','oct_sisa_budget','nov_sisa_budget','dec_sisa_budget','jan_sisa_budget','feb_sisa_budget','mar_sisa_budget','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
