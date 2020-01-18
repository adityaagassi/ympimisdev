<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcYmmj extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'nomor','tgl_form','judul','tgl_kejadian','lokasi','material_number','material_description','no_invoice','qty_cek','qty_ng','presentase_ng','detail','penanganan','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
