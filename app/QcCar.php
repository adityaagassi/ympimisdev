<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcCar extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'cpar_no','deskripsi','tinjauan','tindakan','penyebab','perbaikan','created_by'
	];
}
