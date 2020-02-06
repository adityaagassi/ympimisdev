<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkshopTempProcess extends Model{

	protected $fillable = [
		'tag','operator','started_at'
	];
}
