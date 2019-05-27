<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StampHierarchy extends Model
{
  	use SoftDeletes;
    
	protected $fillable = [
		'model', 'finished', 'janean', 'upc', 'created_by'
	];
}
