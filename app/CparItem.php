<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CparItem extends Model
{
    
	protected $fillable = [
		'id_cpar','item','item_desc','detail','jml_cek','jml_ng','presentase_ng','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function cpar()
	{
		return $this->belongsTo('App\CparDepartment', 'id_cpar')->withTrashed();
	}
}

