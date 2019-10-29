<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityList extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'activity_name', 'activity_alias', 'frequency', 'department_id', 'activity_type', 'created_by'
	];

	public function departments()
    {
        return $this->belongsTo('App\Department', 'department_id', 'id')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
