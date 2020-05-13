<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushBlockRecorderResume extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'remark','push_block_id_gen','check_date', 'injection_date_head','mesin_head','injection_date_block','mesin_block','product_type','head','block','push_pull_ng_name','push_pull_ng_value','height_ng_name','height_ng_value','jumlah_cek','pic_check','notes','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
