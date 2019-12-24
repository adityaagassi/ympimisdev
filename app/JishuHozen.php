<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JishuHozen extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'activity_list_id','jishu_hozen_point_id','department','subsection','date','month','foto_aktual','pic','leader','foreman','created_by'
	];

	public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id')->withTrashed();
    }

    public function jishu_hozen_point()
    {
        return $this->belongsTo('App\JishuHozenPoint', 'jishu_hozen_point_id', 'id')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
