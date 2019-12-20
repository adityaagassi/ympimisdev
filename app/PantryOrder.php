<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class PantryOrder extends Model
{
    protected $fillable = [
		'pemesan', 'minuman', 'keterangan', 'gula', 'jumlah', 'tempat', 'status', 'tgl_pesan', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
