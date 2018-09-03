<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialVolume extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'material_number', 'category', 'lot', 'width', 'length', 'height', 'created_by'
	];

		public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}
    //
}
