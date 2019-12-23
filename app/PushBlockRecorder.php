<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushBlockRecorder extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'push_block_code','check_date', 'injection_date','product_type','head','block','push_pull','judgement','pic_check','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
