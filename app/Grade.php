<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
	use SoftDeletes;
    protected $fillable = [
		'id', 'grade_code', 'grade_name'
	];
}
