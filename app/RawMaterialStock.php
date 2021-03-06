<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterialStock extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'material_number', 'material_description', 'storage_location', 'quantity', 'stock_date', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
