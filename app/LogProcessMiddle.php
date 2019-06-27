<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogProcessMiddle extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'op_prod', 'tag', 'material_number', 'location', 'qty', 'group_code', 'op_kensa', 'prod_date', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}
}
