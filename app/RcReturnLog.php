<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RcReturnLog extends Model
{
    protected $fillable = [
		'material_number','material_description','part_code','part_type','color','quantity','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
