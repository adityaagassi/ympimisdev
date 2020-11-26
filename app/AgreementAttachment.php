<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementAttachment extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'agreement_id', 'file_name', 'created_by'
	];
}
