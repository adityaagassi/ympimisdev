<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'material_number', 'material_description', 'base_unit', 'issue_storage_location', 'mrpc', 'valcl', 'origin_group_code', 'hpl', 'category', 'model', 'created_by', 'std_price'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function origingroup()
	{
		return $this->belongsTo('App\OriginGroup', 'origin_group_code', 'origin_group_code')->withTrashed();
	}
    //
}