<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialListByModel extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'material_parent', 'material_parent_description', 'material_child', 'material_child_description', 'uom', 'purg', 'usage', 'vendor', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
