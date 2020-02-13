<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushBlockRecorderTemp extends Model
{
    protected $fillable = [
		'push_block_code','check_date', 'injection_date_head', 'mesin_head', 'injection_date_block', 'mesin_block','product_type','head','block','push_pull','judgement','ketinggian','judgement2','pic_check','notes','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
