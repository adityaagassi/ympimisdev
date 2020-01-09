<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClinicPatient extends Model{

	protected $connection = 'clinic';
	protected $table = 'patient_list';

	protected $fillable = [
		'employee_id', 'tanggal', 'durasi_detik', 'in_time', 'last_seen', 'status', 'id_mesin', 'uid' , 'first_ref_index', 'last_ref_index', 'flag', 'create_ts', 'note'
	];

}
