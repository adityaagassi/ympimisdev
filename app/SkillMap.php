<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkillMap extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id','skill_code','process','location','value','remark','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
