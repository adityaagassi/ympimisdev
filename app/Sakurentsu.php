<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sakurentsu extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'applicant','file','upload_date','target_date','file_translate','translator','translate_date','sakurentsu_number','category','pic','status','position','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
