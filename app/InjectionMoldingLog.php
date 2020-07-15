<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionMoldingLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'tag_molding','mesin','part','color','cavity','start_time','end_time', 'running_shot','total_running_shot','ng_name','ng_count','status','status_maintenance','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
