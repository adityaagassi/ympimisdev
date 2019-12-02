<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcCpar extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'cpar_no','kategori','employee_id','lokasi','tgl_permintaan','tgl_balas','file','via_komplain','department_id','sumber_komplain','status_code','destination_code','vendor','email_status','email_send_date','staff','leader','chief','foreman','manager','dgm','gm','posisi','checked_chief','checked_foreman','checked_manager','approved_dgm','approved_gm','received_manager','progress','created_by','tindakan','cost'
	];
}
