<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jig extends Model
{
	use softDeletes;

	protected $fillable = [
		'jig_id', 'jig_index', 'jig_name','jig_alias', 'category', 'type', 'file_name', 'jig_tag','check_period', 'created_by'
	];
}
