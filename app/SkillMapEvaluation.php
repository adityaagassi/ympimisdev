<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkillMapEvaluation extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'evaluation_code','employee_id','skill_code','process','location','from_value','to_value','evaluation_point','evaluation_value','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
