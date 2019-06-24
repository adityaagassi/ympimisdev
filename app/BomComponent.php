<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BomComponent extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'material_parent', 'material_child', 'usage', 'created_by'
	];

	public function material_parent()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_parent')->withTrashed();
	}

	public function material_child()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_child')->withTrashed();
	}

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
