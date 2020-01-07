<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkshopTempProcess extends Model{

	protected $fillable = [
		'tag', 'process_name','started_at'
	];
}
