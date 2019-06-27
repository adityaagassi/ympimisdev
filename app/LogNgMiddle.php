<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogNgMiddle extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'op_kensa', 'prod_date', 'op_prod', 'tag', 'material_number', 'location', 'ng_name', 'group_code', 'qty', 'remark', 'created_by'
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
