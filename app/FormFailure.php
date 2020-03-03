<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormFailure extends Model
{
    protected $fillable = [
		'kategori','employee_id','employee_name','section','department','tanggal','judul','penyebab','penanganan','tindakan','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
