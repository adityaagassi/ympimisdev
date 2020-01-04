<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushBlockRecorderResume extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'remark','check_date', 'injection_date','product_type','head','block','push_pull_ng_name','push_pull_ng_value','height_ng_name','height_ng_value','pic_check','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
