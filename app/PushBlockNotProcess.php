<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushBlockNotProcess extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'push_block_code', 'date','process','reason','remark','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
