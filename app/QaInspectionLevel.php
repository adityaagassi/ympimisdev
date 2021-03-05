<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaInspectionLevel extends Model
{
    protected $fillable = [
		'inspection_level', 'created_by'
	];
}
