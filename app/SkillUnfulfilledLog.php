<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkillUnfulfilledLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id','process','location','skill_code','value','required','remark','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
