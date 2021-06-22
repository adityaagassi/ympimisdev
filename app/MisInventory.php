<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisInventory extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'category', 'serial_number', 'device', 'description', 'project', 'location','qty','used_by', 'remark', 'receive_date', 'condition', 'created_by', 'id_order'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
