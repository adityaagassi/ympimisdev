<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaCheck extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'activity_list_id','area_check_point_id','department','subsection', 'date','condition','pic','leader','foreman','created_by'
	];

	public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id')->withTrashed();
    }

    public function area_check_point()
    {
        return $this->belongsTo('App\AreaCheckPoint', 'area_check_point_id', 'id')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
