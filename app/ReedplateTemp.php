<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedplateTemp extends Model
{
    protected $fillable = [
		'major', 'minor', 'reader', 'distance', 'mulai', 'selesai'
	];
}
