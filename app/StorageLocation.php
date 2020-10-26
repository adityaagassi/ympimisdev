<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageLocation extends Model
{
	protected $fillable = [
		'storage_location', 'location', 'origin_group', 'created_by'
	];
}
