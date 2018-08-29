<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'material_number', 'material_description', 'base_unit', 'issue_storage_location', 'origin_group_code', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by');
	}

	public function origingroup()
	{
		return $this->belongsTo('App\OriginGroup', 'origin_group_code', 'origin_group_code');
	}
    //
}