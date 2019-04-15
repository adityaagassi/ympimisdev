<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisInvestment extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'project', 'description', 'start_date', 'finish_date', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
