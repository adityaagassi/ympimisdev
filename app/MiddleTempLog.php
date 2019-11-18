<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleTempLog extends Model
{
    protected $fillable = [
		'operator_id','material_number','quantity','location'
	];

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}
}
