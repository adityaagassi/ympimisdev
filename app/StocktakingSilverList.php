<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StocktakingSilverList extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'material_number', 'material_description', 'category', 'storage_location', 'quantity_check', 'quantity_final', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}