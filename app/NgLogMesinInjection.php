<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NgLogMesinInjection extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'mesin','pic','part_name','color','start_time','end_time','jumlah_shot','running_time','ng_name','ng_count','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
