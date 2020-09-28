<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccActualLog extends Model
{
    use SoftDeletes;
	
    protected $fillable = [	
		'periode','document_no','type','description','reference','gl_number','post_date','month_date','local_amount','local_currency','amount','currency','budget_no','investment_no','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
