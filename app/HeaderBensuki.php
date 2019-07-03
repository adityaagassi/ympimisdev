<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HeaderBensuki extends Model
{
   use SoftDeletes;

	protected $fillable = [
		'model','kode_op_bensuki','nik_op_bensuki','kode_op_plate','nik_op_plate','shift','mesin','created_by','line'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
