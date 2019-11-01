<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusMesinInjection extends Model
{
    use SoftDeletes;
    
	protected $fillable = [
		'mesin', 'status','created_by'
	];
}
