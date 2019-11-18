<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcCar extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'cpar_no','pic','posisi','email_status','email_send_date','deskripsi','tinjauan','tindakan','penyebab','perbaikan','created_by'
	];

	public function employee_pic()
    {
        return $this->belongsTo('App\Employee', 'pic', 'employee_id')->withTrashed();
    }

    public function car_cpar()
    {
        return $this->belongsTo('App\QcCpar', 'cpar_no', 'cpar_no')->withTrashed();
    }
}
