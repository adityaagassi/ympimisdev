<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkillMutationLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id','process_from','process_to','location','remark','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
