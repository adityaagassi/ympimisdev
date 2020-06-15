<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccBudget extends Model
{
	use SoftDeletes;
	
    protected $fillable = [
		'periode','budget_no','department','description','amount','env','purpose','pic','account_name','category','apr','may','jun','jul','aug','sep','oct','nov','dec','jan','feb','mar','adj_frc','apr_f','may_f','jun_f','jul_f','aug_f','sep_f','oct_f','nov_f','dec_f','jan_f','feb_f','mar_f','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
