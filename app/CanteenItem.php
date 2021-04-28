<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanteenItem extends Model
{
	protected $fillable = [
		'kode_item','kategori','deskripsi','uom','harga','currency','remark','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
