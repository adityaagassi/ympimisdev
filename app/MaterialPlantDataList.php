<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialPlantDataList extends Model
{
    protected $fillable = [
		'material_number', 'material_description', 'bun', 'spt', 'storage_location', 'valcl', 'standard_price', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
