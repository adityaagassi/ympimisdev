<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDocument extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'document_number', 'employee_id', 'valid_from', 'valid_to', 'status', 'condition', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
