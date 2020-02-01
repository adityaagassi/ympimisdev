<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RcCameraKangoLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'model','check_date','value_check','judgement','remark','file','pic_check','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
