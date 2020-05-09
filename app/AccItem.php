<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccItem extends Model
{
    protected $fillable = [
		'no_item','kategori','nama','uom','detail_1','detail_2','harga','lot','moq','leadtime','coo','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
