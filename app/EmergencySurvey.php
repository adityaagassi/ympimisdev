<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmergencySurvey extends Model
{
    protected $fillable = [
		'tanggal','employee_id',  'name', 'jawaban', 'nama', 'hubungan', 'keterangan'
	];
}
