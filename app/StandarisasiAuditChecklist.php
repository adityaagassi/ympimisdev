<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandarisasiAuditChecklist extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'kategori','lokasi','klausul','point_judul','point_question','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
