<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcRequest extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'subject','judul','tanggal','section_from','section_to','target','jumlah','waktu','aksi','approval','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
	
}
