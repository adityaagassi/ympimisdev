<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenterHistory extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'cost_center_code','cost_center_name','count','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
