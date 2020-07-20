<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionTransaction extends Model
{
    use softDeletes;

	protected $fillable = [
		'material_number', 'location', 'quantity', 'status','operator_id', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
