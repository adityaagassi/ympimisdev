<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PantryLog extends Model
{
    protected $fillable = [
		'pemesan', 'minuman', 'keterangan', 'gula', 'jumlah', 'tempat', 'tgl_pesan', 'tgl_dibuat', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
