<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcCpar extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'cpar_no','kategori','employee_id','lokasi','tgl_permintaan','tgl_balas','file','via_komplain','department_id','sumber_komplain','status_code','vendor','destination_code','created_by'
	];
}
