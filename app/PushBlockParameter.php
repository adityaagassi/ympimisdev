<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushBlockParameter extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'push_block_code','push_block_id_gen','reason','check_date', 'product_type', 'mesin', 'molding','nh','h1','h2','h3','dryer','mtc_temp','mtc_press','chiller_temp','chiller_press','clamp','ph4','ph3','ph2','ph1','trh3','trh2','trh1','vh','pi','ls10','vi5','vi4','vi3','vi2','vi1','ls4','ls4d','ls4c','ls4b','ls4a','ls5','ve1','ve2','vr','ls31a','ls31','srn','rpm','bp','tr1inj','tr3cool','tr4int','mincush','fill','circletime','notes','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
