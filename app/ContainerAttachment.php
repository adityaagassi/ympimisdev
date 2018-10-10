<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContainerAttachment extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'container_id', 'file_name', 'file_path', 'created_by'
	];
    //
}
