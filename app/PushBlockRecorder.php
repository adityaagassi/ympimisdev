<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushBlockRecorder extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'push_block_code','push_block_id_gen','check_date', 'injection_date_head', 'mesin_head', 'injection_date_block', 'mesin_block','product_type','head','block','push_pull','judgement','ketinggian','judgement2','pic_check','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
