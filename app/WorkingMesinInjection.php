<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingMesinInjection extends Model
{
    use SoftDeletes;
    
	protected $fillable = [
		'mesin', 'part', 'color', 'model', 'qty', 'created_by'
	];
}
