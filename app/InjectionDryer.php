<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionDryer extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'dryer','machine','material_number','material_description','color','qty','lot_number','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
