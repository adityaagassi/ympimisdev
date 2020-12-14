<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sakurentsu extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'sakurentsu_number', 'title_jp', 'title', 'applicant','file','upload_date','target_date','file_translate','translator','translate_date','category','pic','status','position','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
