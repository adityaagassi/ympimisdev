<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormFailure extends Model
{
    protected $fillable = [
		'employee_id','employee_name','tanggal_kejadian','lokasi_kejadian','equipment','grup_kejadian','judul','loss','kategori','deskripsi','penanganan','tindakan','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
