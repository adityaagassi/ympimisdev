<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcRequestItem extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'id_request','item','item_desc','supplier','detail','jml_cek','jml_ng','presentase_ng','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function request()
	{
		return $this->belongsTo('App\QcRequest', 'id_request')->withTrashed();
	}
}
