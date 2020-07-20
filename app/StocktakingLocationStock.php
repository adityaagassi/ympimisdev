<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StocktakingLocationStock extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'material_number', 'material_description', 'storage_location', 'unrestricted', 'download_date', 'download_time', 'stock_date', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}

