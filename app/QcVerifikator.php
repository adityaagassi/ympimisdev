<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcVerifikator extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'department_id','verifikatorchief','verifikatorforeman','verifikatorcoordinator','created_by'
	];

	public function department()
    {
    	return $this->belongsTo('App\Department', 'department_id', 'id')->withTrashed();
    }

}
